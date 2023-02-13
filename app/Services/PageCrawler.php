<?php

namespace App\Services;

use App\Models\{
    CrawlPageData,
    CrawlData
};
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use DOMDocument;

class PageCrawler
{
    /**
     * Initialize the crawling, compute the crawling results and the returns the result
     * @return array An array of crawling results
     */
    public static function init(int $no_of_desired_results,  string $url): array
    {           
        $crawl_data = new CrawlData();
        $crawl_data->setUrl($url);
        $crawl_data->setInternalLink($url);
        self::crawlPages($no_of_desired_results, $crawl_data);
        return $crawl_data->getCrawlData();
    }

    /**
     * This function is to crawl the pages wiht multiple operations such as check URL validation, gte HTTP content, perform extraction from received content.
     * @return void
     */
    private static function crawlPages(int $no_of_desired_results, CrawlData $crawl_data) : void
    {
        $before_url_set = count($crawl_data->getAllInternalLinks());        
        foreach($crawl_data->getAllInternalLinks() as $link)
        {
            if (!self::moveToNextIfUrlInvalid($link)) {
                continue;
            }
            
            $init_time = microtime(true);
            $http_content = Http::get($link);
            $page_loading_time = microtime(true) - $init_time;
            $http_content_hash = hash('md5', $http_content->body());
            if ( self::moveToNextIfAlreadyCrawled($link, $http_content_hash, $crawl_data) ) {
                continue;
            }

            $crawl_data->setCrawledPageLink($link);
            $crawl_data->setCrawledPageHash($http_content_hash);
            $crawl_data->setPageLoadingTime($page_loading_time);
            $crawl_data->setCrawledPageData( (new CrawlPageData($link, $http_content->status()))->getCrawlPageData() );
            
            if ( self::checkIfPageContectIsSuccefullyFetched($http_content) ) {                    
                $dom_doc = new DOMDocument();                
                $dom_doc->loadHTML($http_content->body(), LIBXML_NOERROR);
                self::setCrawlData($link, $crawl_data, $dom_doc);
            }

            --$no_of_desired_results;
            if($no_of_desired_results<1){                
                break;
            }
        }        
        $after_url_set = count($crawl_data->getAllInternalLinks());
        if (self::checkIfDesiredResultsCompleted($no_of_desired_results, $before_url_set, $after_url_set)) {
            self::crawlPages($no_of_desired_results, $crawl_data);
        }
        return;
    }

    /**
     * Check if the URL is a valid URL
     * @param string $url The URL that needs to validated
     * @return boolean Return True if valid and false if not valid
     */
    private static function moveToNextIfUrlInvalid($url)
    {
        return (filter_var($url, FILTER_VALIDATE_URL) !== FALSE);
    }

    /**
     * Check if page is already crawled
     * @param string $link The URL of page to be crawled
     * @param string $http_content_hash The Hash of page to be crawled
     * @param object $crawl_data The object of crawled data
     * @return boolean Return True if crawled and false if not crawled
     */
    private static function moveToNextIfAlreadyCrawled($link, $http_content_hash, CrawlData $crawl_data)
    {
        if( in_array($link, $crawl_data->getCrawledPageLinks()) ){
            return TRUE;
        }
        if( in_array($http_content_hash, $crawl_data->getCrawledPageHashes()) ){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Check if page contect is succefully fetched
     * @param object $http_content The http object of fetched content
     * @return boolean Return True if status code is OK and false if not
     */
    private static function checkIfPageContectIsSuccefullyFetched($http_content)
    {
        if ($http_content->ok() === true && $http_content->status() === Response::HTTP_OK ) { 
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Extract data from dom object
     * @param string link The crawled page link
     * @param object $crawl_data The Crawled data object
     * @param object $dom_doc The DOMDocument object
     * @return void
     */
    private static function setCrawlData(string $link, CrawlData $crawl_data, DOMDocument $dom_doc) : void
    {
        self::setPageTitle($crawl_data, $dom_doc);
        self::setImages($crawl_data, $dom_doc);
        self::setInternalExternalLinks($link, $crawl_data, $dom_doc);
        self::setPageWordCount($crawl_data, $dom_doc);
    }

    /**
     * Extract and set page title into crawl data model
     * @param object $crawl_data The Crawled data object
     * @param object $dom_doc The DOMDocument object
     * @return void
     */
    private static function setPageTitle(CrawlData $crawl_data, DOMDocument $dom_doc) : void
    {
        $crawl_data->setPageTitle( $dom_doc->getElementsByTagName('title')[0]->nodeValue ?? '' );
    }

    /**
     * Extract and set images into crawl data model
     * @param object $crawl_data The Crawled data object
     * @param object $dom_doc The DOMDocument object
     * @return void
     */
    private static function setImages(CrawlData $crawl_data, DOMDocument $dom_doc) : void
    {
        foreach ($dom_doc->getElementsByTagName('img') as $img) {
            if( $img->getAttribute('src')!='' || $img->getAttribute('data-src')){
                $crawl_data->setImage( $img->getAttribute('src') ?? $img->getAttribute('data-src') );
            }
        }
    }

    /**
     * Extract and set internal and external links into crawl data model
     * @param string link The crawled page link
     * @param object $crawl_data The Crawled data object
     * @param object $dom_doc The DOMDocument object
     * @return void
     */
    private static function setInternalExternalLinks(string $link, CrawlData $crawl_data, DOMDocument $dom_doc) : void
    {
        $parsed_url = parse_url($link);
        foreach ($dom_doc->getElementsByTagName('a') as $anchor) {
            $href = $anchor->getAttribute('href') ?? null;
            if ($href != "") {
                if ($href[0] === "/") {
                    $href = "{$parsed_url['scheme']}://{$parsed_url['host']}$href";
                    $crawl_data->setInternalLink($href);
                } elseif ($href[0] === "#" || parse_url($href, PHP_URL_HOST) === parse_url($link, PHP_URL_HOST)) {
                    $crawl_data->setInternalLink($href);
                } else {
                    $crawl_data->setExternalLink($href);
                }
            }
        }
    }

    /**
     * Extract and set page words count into crawl data model
     * @param object $crawl_data The Crawled data object
     * @param object $dom_doc The DOMDocument object
     * @return void
     */
    private static function setPageWordCount(CrawlData $crawl_data, DOMDocument $dom_doc) : void
    {
        $elements = $dom_doc->getElementsByTagName('*');
        foreach ($elements as $element) {                    
            $child_el = $element->childNodes->length;
            for ($a = 0; $a < $child_el; $a++) {
                if ($element->childNodes->item($a)->nodeType === 3) {
                    $content = $element->childNodes->item($a)->textContent;
                    $content = self::removePunctuations($content);
                    $content = self::removeWhiteSpaces($content);
                    $crawl_data->setPagesWordsCount( count(array_filter(explode(' ', $content))) );
                }
            }
        }
    }

    /**
     * Function to remove punctuations from string
     * @param string text The content string
     * @return string Return cleaned string
     */
    private static function removePunctuations(string $content) : string
    {
        return str_replace(array('!', ',', '?', ' / ', '.'), '', $content);
    }

    /**
     * Function to remove extra white spaces from string
     * @param string text The content string
     * @return string Return cleaned string
     */
    private static function removeWhiteSpaces(string $content) : string
    {
        return preg_replace('/\s+/', ' ', $content);
    }

    /**
     * Check if the desired results count is completed
     * @param string $no_of_desired_results Reducing number of desired results
     * @param string $before_url_set Internal links count before crawling the page
     * @param string $after_url_set Internal links count after adding the crawled links
     * @return boolean Return True if completed and false if not
     */
    private static function checkIfDesiredResultsCompleted(int $no_of_desired_results, int $before_url_set, int $after_url_set)
    {
        if ($no_of_desired_results>0 && ($after_url_set-$before_url_set)>1) { 
            return TRUE;
        }
        return FALSE;
    }
}