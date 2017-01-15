<?php
namespace Interview;

use Interview\ImageSearcher\ImageSearcher;
use Interview\ImageSearcher\YandexImageSearcher;
use Interview\SimpleImage\SimpleImage;
use Silex\Application;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

/** @var Application $app */

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array());
});

$app->post('/send', function (Request $request) use ($app) {
    $imagesCount = 100;
    $data = json_decode($request->getContent(), true);

    $imageSearcher = new ImageSearcher(new YandexImageSearcher());
    $imageUrls = $imageSearcher->search($data['searchQuery'], $imagesCount);

    $SimpleImage = new SimpleImage();

    $images = [];
    $imagesNumber = 0;

    $color = !preg_match('/^#[a-f0-9]{6}\b$/i', $data['color'])
        ? $SimpleImage->getColorByName($data['color']) : $data['color'];

    if (empty($color)) {
        throw new Exception('wrong color');
    }

    foreach ($imageUrls as $key => $url) {
        if ($imagesNumber >= $imagesCount) {
            break;
        }
        try {
            $SimpleImage->load($url);
            $SimpleImage->resizeToWidth(100);
            $images[] = [
                'url'   => $url,
                'image' => $SimpleImage->save(),
                'coff'  => $SimpleImage->color($color),
            ];
        } catch (Exception $e) {
            continue;
        }
        $imagesNumber++;
    }

    usort($images, function ($a, $b) {
        return $a['coff'] < $b['coff'];
    });
    return json_encode(['images' => $images]);
});

