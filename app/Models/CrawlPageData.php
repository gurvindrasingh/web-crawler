<?php

namespace App\Models;

class CrawlPageData
{
    /**
     * The url of the crawled page.
     * @var string
     */
    private string $crawled_page_link;

    /**
     * The Http Status Code returned by crawling request.
     * @var int
     */
    private int $http_status_code;
    
    /**
     * Constructor to set crawling url.
     * @param string $link The URL that need to crawl
     */
    public function __construct(string $link, int $http_status_code)
    {
        $this->crawled_page_link = $link;
        $this->http_status_code = $http_status_code;
    }

    /**
     * Function to get crawled pages data as an array
     * @return array An array of crawled pages data
     */
    public function getCrawlPageData(): array
    {
        return [
            'page_link' => $this->crawled_page_link,
            'http_status_code' => $this->http_status_code
        ];
    }
}
