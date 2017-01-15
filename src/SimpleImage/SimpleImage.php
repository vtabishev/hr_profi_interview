<?php
namespace Interview\SimpleImage;

use Symfony\Component\Config\Definition\Exception\Exception;

class SimpleImage
{
    /**
     * Процент отклонения от тона
     */
    const DEVIATION_H = 0.2;
    /**
     * Процент отклонения от яркости
     */
    const DEVIATION_V = 0.1;
    private $image;

    /**
     * @param $filename
     */
    public function load($filename)
    {
        $this->image = @imagecreatefromjpeg($filename);
        if (!$this->image) {
            throw new Exception('error');
        }

        $this->pixelsNumber = $this - $this->getWidth() * $this->getHeight();
    }

    /**
     * Возращает процент совпадения цвета
     * Совпадение ищется по цветовому тону, для ахроматического цвета по яркости
     * @param $searchColor
     *
     * @return int|mixed
     */
    public function color($searchColor)
    {
        $pixels = 0;

        list($r, $g, $b) = sscanf($searchColor, "#%02x%02x%02x");
        list($searchH, $searchS, $searchV) = $this->rgb2hsv($r, $g, $b);

        for ($i = 0; $i < $this->getWidth(); $i++) {
            for ($j = 0; $j < $this->getHeight(); $j++) {
                $rgb = imagecolorat($this->image, $i, $j);
                $colors = imagecolorsforindex($this->image, $rgb);

                list($h, $s, $v) = $this->rgb2hsv(
                    $colors['red'],
                    $colors['green'],
                    $colors['blue']);

                if($searchH == 0){
                    if (($searchV-$searchV*self::DEVIATION_V)>$v || $v>($searchV+$searchV*self::DEVIATION_V)){
                        continue;
                    }
                } else {
                    if (($searchH-$searchH*self::DEVIATION_H)>$h || $h>($searchH+$searchH*self::DEVIATION_H)) {
                        continue;
                    }
                }

                $pixels++;
            }
        }

        return (int)($pixels / $this->getPixelsCount() * 100);
    }

    /**
     * ShowImages
     */
    public function save()
    {
        ob_start();
        imagejpeg($this->image);
        $image = base64_encode(ob_get_contents());
        ob_end_clean();

        return $image;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * @return int
     */
    public function getPixelsCount()
    {
        return $this->getHeight() * $this->getWidth();
    }

    /**
     * @param $height
     */
    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param $width
     */
    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * @param $scale
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * @param $width
     * @param $height
     */
    public function resize($width, $height)
    {
        $newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height,
            $this->getWidth(), $this->getHeight());
        $this->image = $newImage;
    }

    /**
     * @param $r
     * @param $g
     * @param $b
     *
     * @return array
     */
    public function rgb2hsv($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0;
        $l = ($max + $min) / 2;
        $d = $max - $min;
        if ($d == 0) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / (1 - abs(2 * $l - 1));
            switch ($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * (($b - $r) / $d + 2);
                    break;
                case $b:
                    $h = 60 * (($r - $g) / $d + 4);
                    break;
            }
        }
        return [(int)$h, (int)($s * 100), (int)($max * 100)];
    }

    public function getColorByName($name)
    {
        $enMap = [
            'aliceblue'            => '#f0f8ff',
            'antiquewhite'         => '#faebd7',
            'aqua'                 => '#00ffff',
            'aquamarine'           => '#7fffd4',
            'azure'                => '#f0ffff',
            'beige'                => '#f5f5dc',
            'bisque'               => '#ffe4c4',
            'black'                => '#000000',
            'blanchedalmond'       => '#ffebcd',
            'blue'                 => '#0000ff',
            'blueviolet'           => '#8a2be2',
            'brown'                => '#a52a2a',
            'burlywood'            => '#deb887',
            'cadetblue'            => '#5f9ea0',
            'chartreuse'           => '#7fff00',
            'chocolate'            => '#d2691e',
            'coral'                => '#ff7f50',
            'cornflowerblue'       => '#6495ed',
            'cornsilk'             => '#fff8dc',
            'crimson'              => '#dc143c',
            'cyan'                 => '#00ffff',
            'darkblue'             => '#00008b',
            'darkcyan'             => '#008b8b',
            'darkgoldenrod'        => '#b8860b',
            'darkgray'             => '#a9a9a9',
            'darkgrey'             => '#a9a9a9',
            'darkgreen'            => '#006400',
            'darkkhaki'            => '#bdb76b',
            'darkmagenta'          => '#8b008b',
            'darkolivegreen'       => '#556b2f',
            'darkorange'           => '#ff8c00',
            'darkorchid'           => '#9932cc',
            'darkred'              => '#8b0000',
            'darksalmon'           => '#e9967a',
            'darkseagreen'         => '#8fbc8f',
            'darkslateblue'        => '#483d8b',
            'darkslategray'        => '#2f4f4f',
            'darkslategrey'        => '#2f4f4f',
            'darkturquoise'        => '#00ced1',
            'darkviolet'           => '#9400d3',
            'deeppink'             => '#ff1493',
            'deepskyblue'          => '#00bfff',
            'dimgray'              => '#696969',
            'dimgrey'              => '#696969',
            'dodgerblue'           => '#1e90ff',
            'firebrick'            => '#b22222',
            'floralwhite'          => '#fffaf0',
            'forestgreen'          => '#228b22',
            'fuchsia'              => '#ff00ff',
            'gainsboro'            => '#dcdcdc',
            'ghostwhite'           => '#f8f8ff',
            'gold'                 => '#ffd700',
            'goldenrod'            => '#daa520',
            'gray'                 => '#808080',
            'grey'                 => '#808080',
            'green'                => '#008000',
            'greenyellow'          => '#adff2f',
            'honeydew'             => '#f0fff0',
            'hotpink'              => '#ff69b4',
            'indianred'            => '#cd5c5c',
            'indigo'               => '#4b0082',
            'ivory'                => '#fffff0',
            'khaki'                => '#f0e68c',
            'lavender'             => '#e6e6fa',
            'lavenderblush'        => '#fff0f5',
            'lawngreen'            => '#7cfc00',
            'lemonchiffon'         => '#fffacd',
            'lightblue'            => '#add8e6',
            'lightcoral'           => '#f08080',
            'lightcyan'            => '#e0ffff',
            'lightgoldenrodyellow' => '#fafad2',
            'lightgray'            => '#d3d3d3',
            'lightgrey'            => '#d3d3d3',
            'lightgreen'           => '#90ee90',
            'lightpink'            => '#ffb6c1',
            'lightsalmon'          => '#ffa07a',
            'lightseagreen'        => '#20b2aa',
            'lightskyblue'         => '#87cefa',
            'lightslategray'       => '#778899',
            'lightslategrey'       => '#778899',
            'lightsteelblue'       => '#b0c4de',
            'lightyellow'          => '#ffffe0',
            'lime'                 => '#00ff00',
            'limegreen'            => '#32cd32',
            'linen'                => '#faf0e6',
            'magenta'              => '#ff00ff',
            'maroon'               => '#800000',
            'mediumaquamarine'     => '#66cdaa',
            'mediumblue'           => '#0000cd',
            'mediumorchid'         => '#ba55d3',
            'mediumpurple'         => '#9370d8',
            'mediumseagreen'       => '#3cb371',
            'mediumslateblue'      => '#7b68ee',
            'mediumspringgreen'    => '#00fa9a',
            'mediumturquoise'      => '#48d1cc',
            'mediumvioletred'      => '#c71585',
            'midnightblue'         => '#191970',
            'mintcream'            => '#f5fffa',
            'mistyrose'            => '#ffe4e1',
            'moccasin'             => '#ffe4b5',
            'navajowhite'          => '#ffdead',
            'navy'                 => '#000080',
            'oldlace'              => '#fdf5e6',
            'olive'                => '#808000',
            'olivedrab'            => '#6b8e23',
            'orange'               => '#ffa500',
            'orangered'            => '#ff4500',
            'orchid'               => '#da70d6',
            'palegoldenrod'        => '#eee8aa',
            'palegreen'            => '#98fb98',
            'paleturquoise'        => '#afeeee',
            'palevioletred'        => '#d87093',
            'papayawhip'           => '#ffefd5',
            'peachpuff'            => '#ffdab9',
            'peru'                 => '#cd853f',
            'pink'                 => '#ffc0cb',
            'plum'                 => '#dda0dd',
            'powderblue'           => '#b0e0e6',
            'purple'               => '#800080',
            'red'                  => '#ff0000',
            'rebeccapurple'        => '#663399',
            'rosybrown'            => '#bc8f8f',
            'royalblue'            => '#4169e1',
            'saddlebrown'          => '#8b4513',
            'salmon'               => '#fa8072',
            'sandybrown'           => '#f4a460',
            'seagreen'             => '#2e8b57',
            'seashell'             => '#fff5ee',
            'sienna'               => '#a0522d',
            'silver'               => '#c0c0c0',
            'skyblue'              => '#87ceeb',
            'slateblue'            => '#6a5acd',
            'slategray'            => '#708090',
            'slategrey'            => '#708090',
            'snow'                 => '#fffafa',
            'springgreen'          => '#00ff7f',
            'steelblue'            => '#4682b4',
            'tan'                  => '#d2b48c',
            'teal'                 => '#008080',
            'thistle'              => '#d8bfd8',
            'tomato'               => '#ff6347',
            'turquoise'            => '#40e0d0',
            'violet'               => '#ee82ee',
            'wheat'                => '#f5deb3',
            'white'                => '#ffffff',
            'whitesmoke'           => '#f5f5f5',
            'yellow'               => '#ffff00',
            'yellowgreen'          => '#9acd32'
        ];

        $ruMap = [
            'черный'                     => '#000000',
            'темно-синий'                => '#000080',
            'темно-голубой'              => '#00008B',
            'умеренно-голубой'           => '#0000CD',
            'голубой'                    => '#0000FF',
            'темно-зеленый'              => '#FFF8DC',
            'зеленый'                    => '#008000',
            'чайный'                     => '#008080',
            'темныйциан'                 => '#008B8B',
            'темныйнебесно-синий'        => '#00BFFF',
            'темно-бирюзовый'            => '#00CED1',
            'умеренныйсиневато-серый'    => '#00FA9A',
            'известковый'                => '#00FF00',
            'весеннийзеленый'            => '#00FF7F',
            'синий'                      => '#00FFFF',
            'ночнойсиний'                => '#191970',
            'тускло-васильковый'         => '#1E90FF',
            'светлыйморской волны'       => '#20B2AA',
            'леснойзеленый'              => '#228B22',
            'морскойволны'               => '#2E8B57',
            'темныйсиневато-серый'       => '#2F4F4F',
            'зеленовато-известковый'     => '#32CD32',
            'умеренныйморской волны'     => '#3CB371',
            'бирюзовый'                  => '#40E0D0',
            'королевскийголубой'         => '#4169E1',
            'голубовато-стальной'        => '#4682B4',
            'темныйсеровато-синий'       => '#483D8B',
            'умеренно-бирюзовый'         => '#48D1CC',
            'индиго'                     => '#4B0082',
            'темно-оливковый'            => '#556B2F',
            'блеклыйсеро-голубой'        => '#5F9EA0',
            'васильковый'                => '#6495ED',
            'умеренно-аквамариновый'     => '#66CDAA',
            'тускло-серый'               => '#696969',
            'серовато-синий'             => '#6A5ACD',
            'тускло-коричневый'          => '#6B8E23',
            'синевато-серый'             => '#708090',
            'светлыйсиневато-серый'      => '#778899',
            'умеренныйсеровато-синий'    => '#7B68EE',
            'зеленойтравы'               => '#7CFC00',
            'фисташковый'                => '#7FFF00',
            'аквамарин'                  => '#7FFFD4',
            'оранжево-розовый'           => '#FA8072',
            'пурпурный'                  => '#800080',
            'оливковый'                  => '#808000',
            'серый'                      => '#808080',
            'небесно-голубой'            => '#87CEEB',
            'светлыйнебесно-синий'       => '#87CEFA',
            'светло-фиолетовый'          => '#8A2BE2',
            'темно-красный'              => '#8B0000',
            'темныйфуксин'               => '#8B008B',
            'старойкожи'                 => '#8B4513',
            'темныйморской волны'        => '#8FBC8F',
            'умеренно-пурпурный'         => '#9370DB',
            'темно-фиолетовый'           => '#9400D3',
            'бледно-зеленый'             => '#98FB98',
            'темно-орхидейный'           => '#9932CC',
            'желто-зеленый'              => '#ADFF2F',
            'светло-зеленый'             => '#9CEE90',
            'охра'                       => '#A0522D',
            'коричневый'                 => '#CD853F',
            'темно-серый'                => '#A9A9A9',
            'светло-голубой'             => '#ADD8E6',
            'бледно-бирюзовый'           => '#AFEEEE',
            'светло-стальной'            => '#B0C4DE',
            'туманно-голубой'            => '#B0E0E6',
            'огнеупорногокирпича'        => '#B22222',
            'темныйкрасно-золотой'       => '#B8860B',
            'умеренно-орхидейный'        => '#BA55D3',
            'розово-коричневый'          => '#BC8F8F',
            'темныйхаки'                 => '#BDB76B',
            'серебристый'                => '#C0C0C0',
            'умеренныйкрасно-фиолетовый' => '#C71585',
            'ярко-красный'               => '#CD5C5C',
            'шоколадный'                 => '#D2691E',
            'желтовато-коричневый'       => '#D2B48C',
            'светло-серый'               => '#D3D3D3',
            'чертополоха'                => '#D8BFD8',
            'орхидейный'                 => '#DA70D6',
            'красногозолота'             => '#DAA520',
            'бледныйкрасно-фиолетовый'   => '#DB7093',
            'малиновый'                  => '#DC143C',
            'светлыйсеро-фиолетовый'     => '#DCDCDC',
            'сливовый'                   => '#DDA0DD',
            'старогодерева'              => '#DEB887',
            'светлыйциан'                => '#E0FFFF',
            'бледно-лиловый'             => '#E6E6FA',
            'темныйоранжево-розовый'     => '#E9967A',
            'фиолетовый'                 => '#EE82EE',
            'бледно-золотой'             => '#EEE8AA',
            'светло-коралловый'          => '#F08080',
            'хаки'                       => '#F0E68C',
            'блекло-голубой'             => '#F0F8FF',
            'свежегомеда'                => '#F0FFF0',
            'лазурь'                     => '#F0FFFF',
            'рыже-коричневый'            => '#F4A460',
            'пшеничный'                  => '#F5DEB3',
            'бежевый'                    => '#F5F5DC',
            'белыйдымчатый'              => '#F5F5F5',
            'мятно-кремовый'             => '#F5FFFA',
            'туманно-белый'              => '#F8F8FF',
            'античныйбелый'              => '#FAEBD7',
            'льняной'                    => '#FAF0E6',
            'старогоконьяка'             => '#FDF5E6',
            'фуксия'                     => '#FF00FF',
            'темно-розовый'              => '#FF1493',
            'красно-оранжевый'           => '#FF4500',
            'томатный'                   => '#FF6347',
            'ярко-розовый'               => '#FF69B4',
            'коралловый'                 => '#FF7F50',
            'темно-оранжевый'            => '#FF8C00',
            'светлыйоранжево-розовый'    => '#FFA07A',
            'оранжевый'                  => '#FFA500',
            'светло-розовый'             => '#FFB6C1',
            'розовый'                    => '#FFC0CB',
            'золотой'                    => '#FFD700',
            'персиковый'                 => '#FFDAB9',
            'грязно-серый'               => '#FFDEAD',
            'болотный'                   => '#FFE4B5',
            'бисквитный'                 => '#FFE4C4',
            'туманно-розовый'            => '#FFE4E1',
            'светло-кремовый'            => '#FFEBCD',
            'дыни'                       => '#FFEFD5',
            'бледныйрозово-лиловый'      => '#FFF0F5',
            'морскойпены'                => '#FFF5EE',
            'лимонный'                   => '#FFFACD',
            'цветочно-белый'             => '#FFFAF0',
            'снежный'                    => '#FFFAFA',
            'желтый'                     => '#FFFF00',
            'светло-желтый'              => '#FFFFE0',
            'слоновойкости'              => '#FFFFF0',
            'белый'                      => '#FFFFFF',
        ];

        $name = mb_strtolower(preg_replace('/\s/', '', $name));

        if (preg_match('/[a-z]+$/', $name)) {
            return $enMap[$name];
        }

        return $ruMap[$name];
    }
}
