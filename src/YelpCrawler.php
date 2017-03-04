<?php


namespace Yelp;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;


class YelpCrawler
{
    private $biz_url = 'https://www.yelp.com/biz/route-66-moving-san-diego';

    public function crawl()
    {

        $html = $this->getPage($this->biz_url);

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $reviews = [];

        $reviews_count = count($crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]"));

        $review_yelp_id = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]")
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
                return trim($node->text());
            });

        $review_date = $crawler->filterXPath(".//*[@id='super-container']/div/div/div[1]/div[5]/div[1]/div[2]/ul/li/div[@data-signup-object]/div[2]/div[1]/div/span/text()[normalize-space()]")
            ->each(function (Crawler $node, $i) {
                return trim($node->text());
            });

        for ($i = 0; $i < $reviews_count; $i++) {
            $reviews[$i] = [
                'username' => $review_username[$i],
                'userpic' => $review_userpic[$i],
                'text' => $review_text[$i],
                'date' => date('Y-m-d', strtotime($review_date[$i])),
                'url' => $this->biz_url.'?hrid='.$review_yelp_id[$i],
                'rating' => $review_rating[$i],
                'yelp_id' => $review_yelp_id[$i]
            ];
        }


        var_dump($reviews);
    }

    private function getPage($url)
    {
        $client = new Client();
        $res = $client->request('GET', $url);
        $html = $res->getBody()->getContents();

        return $html;

    }

    private function getTotalReviewsCount()
    {
        $html = $this->getPage($this->biz_url);

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $reviews_count = $crawler->filterXPath(".//*[@id='wrap']/div[4]/div/div[1]/div/div[2]/div/div[1]/span[@itemprop='reviewCount']")
            ->text();

        return $reviews_count;
    }
}