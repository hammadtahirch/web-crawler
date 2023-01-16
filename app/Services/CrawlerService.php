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
     * @var ReportRepository
     */
    private ReportRepository $reportRepository;

    /**
     * class constructor
     */
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * @param  array  $params
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
     * @param $html
     * @return array
     */
    public function pageWordCount($html): array
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
     * @param  string  $url
     * @return array
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
     * Search links in HTML and crawl them up to given number of pages
     *
     * @param $html
     * @param $startURL
     * @param $pages
     * @return array
     *
     * @throws DOMException
     */
    public function pageLinksCrawl($html, $startURL, $pages): array
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
     * Helps to search system internal and external links
     *
     * @param $html
     * @param $startURL
     * @return array
     */
    public function pageLinkCount($html, $startURL): array
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
     * Helps to search title tag from html
     *
     * @param $html
     * @return false|string
     */
    public function getPageTitle($html): bool|string
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
     * Helps to search image in html
     *
     * @param $html
     * @return array
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
     * @param  array  $params
     * @return float
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
     * @param int $id
     * @return void
     */
    public function deleteRecords(int $id):void
    {
        $this->reportRepository->destroyReport($id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteAvgRecords(int $id):void
    {
        $this->reportRepository->destroyAvgReport($id);
    }

    /**
     * @return Report
     */
    public function getReports():mixed
    {
        return $this->reportRepository->getReports();
    }

    /**
     * @return AvgReport
     */
    public function getAvgReports():mixed
    {
        return $this->reportRepository->getAvgReports();
    }


}
