<?php

namespace App\Models;

class CrawlData
{
    /**
     * The entry point URL that need to be crawled.
     * @var string
     */
    private string $url;

    /**
     * All crawled pages internal urls.
     * @var array
     */
    private array $all_internal_links = array();

    /**
     * All crawled pages external urls.
     * @var array
     */
    private array $all_external_links = array();

    /**
     * All crawled pages links.
     * @var array
     */
    private array $crawled_page_links = array();

    /**
     * All pages crawled data in form of hashes (To compare with already crawled pages).
     * @var array
     */
    private array $crawled_page_hashes = array();

    /**
     * All pages loading time array.
     * @var array
     */
    private array $pages_loading_time = array();

    /**
     * All pages crawled data as an array.
     * @var array
     */
    private array $crawled_pages_data = array();

    /**
     * All crawled pages titles.
     * @var array
     */
    private array $all_page_titles = array();

    /**
     * All crawled pages images urls.
     * @var array
     */
    private array $all_images = array();

    /**
     * All crawled pages words count.
     * @var array
     */
    private array $pages_words_count = array();

    /**
     * Entry point URL getter.
     * @return string 
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Entry point URL setter.
     * @param string $url Entry point URL
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
    
    /**
     * Entry point URL setter.
     * @param string $url The entry point URL
     */
    public function setHttpStatusCode(string $url): void
    {
        $this->url = $url;
    }

    /**
     * All internal links getter.
     * @return array 
     */
    public function getAllInternalLinks(): array
    {
        return $this->all_internal_links;
    }

    /**
     * All internal links setter.
     * @param string $internal_link The internal link from crawled page
     */
    public function setInternalLink(string $internal_link): void
    {
        array_push($this->all_internal_links, $internal_link);
    }

    /**
     * All external links setter.
     * @param string $external_link The external link from crawled page
     */
    public function setExternalLink(string $external_link): void
    {
        array_push($this->all_external_links, $external_link);
    }

    /**
     * All Crawled pages links getter.
     * @return array 
     */
    public function getCrawledPageLinks(): array
    {
        return $this->crawled_page_links;
    }

    /**
     * Crawled pages links setter.
     * @param string $page_link The crawled page link
     */
    public function setCrawledPageLink(string $page_link): void
    {
        array_push($this->crawled_page_links, $page_link);
    }

    /**
     * Crawled page hashes getter.
     * @return array 
     */
    public function getCrawledPageHashes(): array
    {
        return $this->crawled_page_hashes;
    }

    /**
     * Crawled page hashes setter.
     * @param string $page_hash The crawled page hashed string
     */
    public function setCrawledPageHash(string $page_hash): void
    {
        array_push($this->crawled_page_hashes, $page_hash);
    }

    /**
     * Page loading time setter.
     * @param string $page_loading_time The crawled page loading time
     */
    public function setPageLoadingTime(string $page_loading_time): void
    {
        array_push($this->pages_loading_time, $page_loading_time);
    }    

    /**
     * Crawled page data setter.
     * @param string $crawled_page_data The crawled page data
     */
    public function setCrawledPageData(array $crawled_page_data): void
    {
        array_push($this->crawled_pages_data, $crawled_page_data);
    }

    /**
     * Page title setter.
     * @param string $page_title The crawled page title
     */
    public function setPageTitle(string $page_title): void
    {
        array_push($this->all_page_titles, $page_title);
    }
    
    /**
     * Image setter.
     * @param string $page_title Image from crawled page
     */
    public function setImage(string $image): void
    {
        array_push($this->all_images, $image);
    }
    
    /**
     * Page word count setter.
     * @param string $page_title The total words from crawled page
     */
    public function setPagesWordsCount(int $page_word_count): void
    {
        array_push($this->pages_words_count, $page_word_count);
    }

    /**
     * Function to get crawled data as an array
     * @return array An array of crawled data
     */
    public function getCrawlData(): array
    {
        $result['url'] = $this->url;
        $result['no_of_pages_crawled'] = count($this->crawled_pages_data);
        $result['avg_page_load_time'] = number_format( (array_sum($this->pages_loading_time) / $result['no_of_pages_crawled']), 2);
        $result['no_of_unique_images'] = count( array_unique($this->all_images) );
        $result['no_of_unique_internal_links'] = count( array_unique($this->all_internal_links) );
        $result['no_of_unique_external_links'] = count( array_unique($this->all_external_links) );
        $result['avg_title_length'] = str_word_count(implode(' ', $this->all_page_titles));
        $result['avg_word_count'] = number_format( array_sum($this->pages_words_count) / $result['no_of_pages_crawled'], 0);
        $result['crawled_pages_data'] = $this->crawled_pages_data;
        return $result;
    }
}
