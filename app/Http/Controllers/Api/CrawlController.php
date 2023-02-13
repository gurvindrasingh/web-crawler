<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PageCrawler;
use Symfony\Component\HttpFoundation\Response;

class CrawlController extends Controller
{
    private $url = 'https://agencyanalytics.com/';
    private $no_of_pages_to_be_crawled = 6;

    /**
     * Get crawled data.
     * @return Json Response of crawled data
     */
    public function __invoke()
    {
        return response()->json(PageCrawler::init($this->no_of_pages_to_be_crawled, $this->url), Response::HTTP_OK);
    }
}
