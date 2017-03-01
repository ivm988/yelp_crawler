<?php


namespace Yelp;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;


class YelpCrawler
{
    public function crawl()
    {
        $client = new Client();
        $res = $client->request('GET', 'https://www.yelp.com/biz/route-66-moving-and-storage-san-francisco-6');
        $html =  $res->getBody()->getContents();

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $reviews = [];

        for ($i = 0; $i<20; $i++) {

        }

        $reviews[]['yelp_id'] = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]")
            ->each(function (Crawler $node, $i) {
                return trim($node->attr('data-review-id'));
            });

        $review_rating = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]/div[2]/div[1]/div/div/div")
            ->each(function (Crawler $node, $i) {
                $rating_class = trim($node->attr('class'));
                $match = [];
                preg_match('/i-stars--regular-(\d)/i', $rating_class, $match);
                return $match[1];
            });

        $review_username = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]/div[1]/div/div/div[2]/ul/li[1]/a")
            ->each(function (Crawler $node, $i) {
                return trim($node->text());
            });

        $review_userpic = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]/div[1]/div/div/div[1]/div/a/img")
            ->each(function (Crawler $node, $i) {
                return trim($node->attr('src'));
            });

        $review_text = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]/div[2]/div[1]/p")
            ->each(function (Crawler $node, $i) {
                return trim($node->html());
            });

        $review_date = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]/div[2]/div[1]/div/span")
            ->each(function (Crawler $node, $i) {
                return trim($node->text());
            });





        var_dump($reviews);
    }
}