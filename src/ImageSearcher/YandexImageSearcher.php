<?php
namespace Interview\ImageSearcher;

class YandexImageSearcher implements ImageSearcherInterface
{
    const STEP = 1;
    const INITIAL_PAGE_NUMBER = 1;

    /**
     * @inheritdoc
     */
    public function search($searchQuery, $offset = 0)
    {
        $searchQuery = urlencode($searchQuery);
        $offset = (int)$offset;

        $page
            = file_get_contents("https://yandex.ru/images/search?format=json&p={$offset}&text={$searchQuery}&rpt=image&request=%5B%7B%22block%22%3A%22serp-controller%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%2C%7B%22block%22%3A%22serp-list_infinite_yes%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%2C%7B%22block%22%3A%22more_direction_next%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%2C%7B%22block%22%3A%22gallery__items%3Aajax%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%5D");
        if (empty($page)) {
            return [];
        }

        preg_match_all('#preview\\":\[\{\\"url\\":\\"(https?:\/\/[^\,\"\[\]\{\}]*?\.(jpg|jpeg))#i',
            stripslashes($page), $tokens);

        return $tokens[1];
    }

    /**
     * @inheritdoc
     */
    public function getStep()
    {
        return self::STEP;
    }

    /**
     * @inheritdoc
     */
    public function getInitialPageNumber()
    {
        return self::INITIAL_PAGE_NUMBER;
    }
}