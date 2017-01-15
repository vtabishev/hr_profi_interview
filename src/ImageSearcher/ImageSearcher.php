<?php
namespace Interview\ImageSearcher;

class ImageSearcher
{
    /**
     * @var ImageSearcherInterface
     */
    private $imageSearcher;

    /**
     * ImageSearcher constructor.
     *
     * @param ImageSearcherInterface $imageSearcher
     */
    public function __construct(ImageSearcherInterface $imageSearcher)
    {
        $this->imageSearcher = $imageSearcher;
    }

    /**
     * @param string $searchQuery
     * @param int    $imageCount
     *
     * @return array
     */
    public function search($searchQuery, $imageCount)
    {
        $images = [];
        for (
            $offset = $this->imageSearcher->getInitialPageNumber();
            count($images) < $imageCount;
            $offset += $this->imageSearcher->getStep()
        ) {
            $res = $this->imageSearcher->search($searchQuery, $offset);
            if (empty($res)) {
                break;
            }

            $images = array_merge($images, $res);
        }

        return $images;
    }
}