<?php

namespace App\Services;

use App\Models\Eloquent\AvgReport;
use App\Models\Eloquent\Report;
use App\Models\Repository\ReportRepository;
use DOMDocument;
use DOMException;
use Illuminate\Support\Facades\Http;

class CrawlerService
{
    /**
     * Private property to store an instance of the `ReportRepository` class
     *
     * @var ReportRepository
     */
    private ReportRepository $reportRepository;

    /**
     * Constructor to initialize the `ReportRepository`.
     *
     * @param ReportRepository $reportRepository
     */
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * This function helps to parse the page data by the given URL.
     *
     * @param array $params The parameters contains the URL to be parsed and number of pages to be crawled
     * @return void
     *
     * @throws DOMException
     */
    public function parsePageByUrl(array $params): void
    {
        $sumOfLoadTime = 0;
        $noOfPagesCrawled = 0;
        $sumOfAllPagesTitle = 0;
        $sumOfAllPagesTitleCount = 0;
        $collectAllPagesWords = [];

        $response = $this->getHttpClientDetails($params['url']);
        $sumTitle = 0;
        $collectTitleCount = 0;
        if ($response['status'] === 200) {
            $links = $this->pageLinksCrawl($response['html'], $params['url'], $params['pages']);
            foreach ($links as $index => $link) {
                $anchorLinks = $this->pageLinkCount(base64_decode($link['html']), $link['href']);
                if (isset($link['pageTitle']) && $link['pageTitle'] != '') {
                    $sumTitle = $sumTitle + strlen($link['pageTitle']);
                    $collectTitleCount++;
                }
                // collect avg data
                $sumOfLoadTime += $link['time'];
                $noOfPagesCrawled += $index;
                $sumOfAllPagesTitle += $sumTitle;
                $sumOfAllPagesTitleCount += $collectTitleCount;
                $collectAllPagesWords[] = $link['words'];
                //end
                $this->reportRepository->saveReport([
                    'page_link' => $link['href'],
                    'status_code' => $link['status_code'],
                    'images_links' => count(array_unique($link['images'])),
                    'internal_links' => count($anchorLinks['internal']),
                    'external_links' => count($anchorLinks['external']),
                    'page_load_time' => round($link['time'], 3),
                    'word_count' => array_sum(array_values($link['words'])),
                    'title_length' => round(($sumTitle / $collectTitleCount), 3),
                ]);
            }
            //insert all ave data into table
            //avg data is not 100% accurate. there is always room to improve it.
            $this->reportRepository->saveAvgReport([
                'site_link' => $params['url'],
                'avg_page_load_time' => round(($sumOfLoadTime / $noOfPagesCrawled), 3),
                'avg_title_length' => round(($sumOfAllPagesTitle / $sumOfAllPagesTitleCount), 3),
                'avg_world_count' => $this->avgWordCountForAllPages($collectAllPagesWords),
                'crawled_pages' => $noOfPagesCrawled,
            ]
            );
            //end
        }
    }

    /**
     * This function counts the number of words in the given HTML content.
     *
     * @param string $html The HTML content.
     * @return array An array containing the word counts.
     */
    public function pageWordCount(string $html): array
    {
        // Get rid of style, script etc
        $search = [
            '@<script[^>]*?>.*?</script>@si',
            '@<head>.*?</head>@siU',
            '@<style[^>]*?>.*?</style>@siU',
            '@<![\s\S]*?--[ \t\n\r]*>@',
        ];
        $contents = preg_replace($search, '', $html);
        $lowerCaseWorldCount = array_map('strtolower', str_word_count(strip_tags($contents), 1));

        return array_count_values($lowerCaseWorldCount);
    }

    /**
     * Retrieve HTTP Client Details
     * This function retrieves details of the HTTP client including load time, HTTP status and HTML body of the response.
     *
     * @param string $url URL to retrieve details from
     * @return array Details of the HTTP client response
     */
    public function getHttpClientDetails(string $url): array
    {
        $time_start = microtime(true);
        $response = Http::get($url);
        $time_end = microtime(true);
        $loadTime = round(($time_end - $time_start), 3);

        return [
            'loadTime' => $loadTime,
            'status' => $response->status(),
            'html' => $response->body(),
        ];
    }

    /**
     * Used to crawl a given starting URL and return information about the links found on that page.
     *
     * @param string $html - HTML code of the page to be crawled.
     * @param string $startURL - URL of the page to start crawling.
     * @param int $pages - Maximum number of pages to crawl.
     * @return array - An array of information about the links found on the page.
     *
     * @throws DOMException
     */
    public function pageLinksCrawl(string $html, string $startURL, int $pages): array
    {
        $parseStart = parse_url($startURL);
        $baseStart = $parseStart['scheme'].'://'.$parseStart['host'];

        $htmlDom = new DOMDocument;
        @$htmlDom->loadHTML($html);

        $links = $htmlDom->getElementsByTagName('a');
        $extractedPages = [];

        $i = 0;
        foreach ($links as $link) {
            if ($i >= $pages) {
                continue;
            } else {
                if (! in_array($link, $extractedPages)) {
                    $linkText = $link->nodeValue;
                    $linkHref = $link->getAttribute('href');

                    if (strlen(trim($linkHref)) == 0) {
                        continue;
                    }
                    if ($linkHref[0] == '#') {
                        continue;
                    }
                    $parseCurrent = parse_url($linkHref);
                    if (! isset($parseCurrent['scheme']) || ! isset($parseCurrent['host'])) {
                        $linkHref = $baseStart.$parseCurrent['path'];
                        $parseCurrent = parse_url($linkHref);
                    }

                    $response = $this->getHttpClientDetails($linkHref);

                    //this portion is not  100% correct always room to improve it.
                    $words = $this->pageWordCount($response['html']);
                    $totalWords = 0;
                    foreach ($words as $key => $word) {
                        $totalWords += $word;
                    }

                    //extracted Pages from parsed linked
                    $extractedPages[] = [
                        'pageTitle' => $this->getPageTitle($response['html']),
                        'linkTitle' => trim(preg_replace("/\s+/", ' ', $linkText)),
                        'href' => $linkHref,
                        'status_code' => $response['status'],
                        'internal_external' => ($parseCurrent['host'] == $parseStart['host'] ? 'int' : 'ext'),
                        'time' => round($response['loadTime'], 3),
                        'html' => base64_encode($response['html']),
                        'images' => $this->getPageImages($response['html']),
                        'words' => $words,
                    ];
                }

                $i++;
            }
        }

        return $extractedPages;
    }

    /**
     * Helps to search system internal and external links and remove duplicates
     *
     * @param string $html      The HTML code to extract links from.
     * @param string $startURL  The starting URL used to fix and identify internal and external links.
     * @return array An array of internal and external links found in the HTML.
     */
    public function pageLinkCount(string $html, string $startURL): array
    {
        // Retreive info on the start URL to fix links we retreive
        $parseStart = parse_url($startURL);
        $baseStart = $parseStart['scheme'].'://'.$parseStart['host'];

        $htmlDom = new DOMDocument;
        @$htmlDom->loadHTML($html);

        //Extract the links from the HTML.
        $links = $htmlDom->getElementsByTagName('a');

        $internalLinks = [];
        $externalLinks = [];

        //Loop through the DOMNodeList.
        $i = 1;
        foreach ($links as $link) {
            $linkHref = $link->getAttribute('href');

            if (strlen(trim($linkHref)) == 0) {
                continue;
            }
            if ($linkHref[0] == '#') {
                continue;
            }
            $parseCurrent = parse_url($linkHref);
            if (! isset($parseCurrent['scheme']) || ! isset($parseCurrent['host'])) {
                $linkHref = $baseStart.$parseCurrent['path'];
                $parseCurrent = parse_url($linkHref);
            }
            if (rtrim($linkHref, '/') == rtrim($startURL, '/')) {
                continue;
            }

            if ($parseCurrent['host'] == $parseStart['host']) {
                $internalLinks[] = $linkHref;
            } else {
                $externalLinks[] = $linkHref;
            }
            $i++;
        }

        return [
            'internal' => array_unique($internalLinks),
            'external' => array_unique($externalLinks),
        ];
    }

    /**
     * Helps to search title tag from html content
     *
     * @param string $html The html content of the page
     * @return bool|string The title of the page if it exists, false otherwise.
     */
    public function getPageTitle(string $html): bool|string
    {
        $htmlDom = new DOMDocument;
        @$htmlDom->loadHTML($html);
        $list = $htmlDom->getElementsByTagName('title');
        if ($list->length > 0) {
            return trim(preg_replace("/\s+/", ' ', $list->item(0)->textContent));
        }

        return false;
    }

    /**
     * Search image from html content and return array of images.
     *
     * @param string $html The HTML content as a string
     * @return array An array of image sources (URLs)
     */
    public function getPageImages($html): array
    {
        $img = [];
        $htmlDom = new DOMDocument;
        @$htmlDom->loadHTML($html);
        $tags = $htmlDom->getElementsByTagName('img');
        foreach ($tags as $tag) {
            if (! in_array($tag->getAttribute('src'), $img) && $tag->getAttribute('src') != '') {
                $img[] = $tag->getAttribute('src');
            }
        }

        return $img;
    }

    /**
     * Calculate the average word count for all pages
     *
     * @param array $params An array of arrays, each containing the word count of a page
     * @return float The average word count of all pages
     */
    public function avgWordCountForAllPages(array $params): float
    {
        $output = [];
        foreach ($params as $arr) {
            foreach ($arr as $key => $value) {
                if (isset($output[$key])) {
                    $output[$key] += $value;
                } else {
                    $output[$key] = $value;
                }
            }
        }

        return round(array_sum(array_values($output)) / count($output), 3);
    }

    /**
     * Deletes the specified report record.
     *
     * @param int $id The id of the report to delete
     * @return void
     */
    public function deleteRecords(int $id): void
    {
        $this->reportRepository->destroyReport($id);
    }

    /**
     * Deletes the specified avg report record.
     *
     * @param int $id The id of the avg report to delete
     * @return void
     */
    public function deleteAvgRecords(int $id): void
    {
        $this->reportRepository->destroyAvgReport($id);
    }

    /**
     * Gets report data from report table.
     *
     * @return Report
     */
    public function getReports(): mixed
    {
        return $this->reportRepository->getReports();
    }

    /**
     * Gets avg report data from table.
     *
     * @return AvgReport
     */
    public function getAvgReports(): mixed
    {
        return $this->reportRepository->getAvgReports();
    }

    /**
     * Removes report and avg report data and clear user session.
     *
     * @return void
     */
    public function deleteDataAndSession(): void
    {
        $this->reportRepository->clearOutDataAndSession();
    }
}
