<?php
/**
 * Based on https://framagit.org/framasoft/framabin/blob/master/lib/vizhash_gd_zero.php
 * SPDX-License-Identifier: Zlib.
 */

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\VizHash;

class VizHash
{
    public static function imageToPng($image): string
    {
        ob_start();
        imagepng($image);
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public static function generateBackground(string $input, int $width, int $height)
    {
        $hash = hash('sha256', $input);

        // We convert the hash into an array of integers.
        $values = [];
        for ($i = 0; $i < strlen($hash); $i = $i + 2) {
            array_push($values, hexdec(substr($hash, $i, 2)));
        }
        $valuesIndex = 0; // to walk the array.

        // Returns a single integer from the $VALUES array (0...255)
        $getInt = function () use ($values, &$valuesIndex) {
            $v = $values[$valuesIndex];
            ++$valuesIndex;
            $valuesIndex %= count($values); // Warp around the array

            return $v;
        };

        // Returns a single integer from the array (roughly mapped to image width)
        $getX = function () use ($getInt, $width): int {
            return (int) ($width * $getInt() / 256);
        };

        // Returns a single integer from the array (roughly mapped to image height)
        $getY = function () use ($getInt, $height) {
            return (int) ($height * $getInt() / 256);
        };

        // Then use these integers to drive the creation of an image.
        $image = imagecreatetruecolor($width, $height);
        imageantialias($image, true); // Use antialiasing (if available)

        $r0 = $getInt();
        $r = $r0;
        $g0 = $getInt();
        $g = $g0;
        $b0 = $getInt();
        $b = $b0;

        // First, create an image with a specific gradient background.
        $op = 'v';
        if (($getInt() % 2) === 0) {
            $op = 'h';
        }

        self::degrade($image, $op, [$r0, $g0, $b0], [0, 0, 0]);

        $drawshape = function (&$image, $action, $color) use ($getInt, $getX, $getY) {
            switch ($action % 7) {
                case 0:
                    imagefilledrectangle($image, $getX(), $getY(), $getX(), $getY(), $color);
                    break;
                case 1:
                case 2:
                    imagefilledellipse($image, $getX(), $getY(), $getX(), $getY(), $color);
                    break;
                case 3:
                    $points = [$getX(), $getY(), $getX(), $getY(), $getX(), $getY(), $getX(), $getY()];
                    imagefilledpolygon($image, $points, 4, $color);
                    break;
                case 4:
                case 5:
                case 6:
                    $start = $getInt() * 360 / 256; $end = $start + $getInt() * 180 / 256;
                    imagefilledarc($image, $getX(), $getY(), $getX(), $getY(), (int) $start, (int) $end, $color, IMG_ARC_PIE);
                    break;
            }
        };

        for ($i = 0; $i < 7; $i = $i + 1) {
            $action = $getInt();
            $color = imagecolorallocate($image, (int) $r, (int) $g, (int) $b);
            $r = ($r0 + $getInt() / 25) % 256;
            $g = ($g0 + $getInt() / 25) % 256;
            $b = ($b0 + $getInt() / 25) % 256;
            $r0 = $r;
            $g0 = $g;
            $b0 = $b;
            $drawshape($image, $action, $color);
        }
        $color = imagecolorallocate($image, $getInt(), $getInt(), $getInt());
        $drawshape($image, $getInt(), $color);

        return $image;
    }

    /**
     * Blend $src on top of $dest, centered, with variable padding and alpha.
     *
     * @param mixed $dest  The image to blend on top of
     * @param mixed $src   The image to to onto $dest
     * @param float $fill  The amount of space $src should occupy in $dest: 0=nothing, 1=all
     * @param float $alpha How transparent $src should be: 0=fully transparent, 1=fully visible
     */
    public static function blendPhoto(&$dest, &$src, float $fill, float $alpha)
    {
        $destWidth = imagesx($dest);
        $destHeight = imagesy($dest);
        $srcWidth = imagesx($src);
        $srcHeight = imagesy($src);
        $boundingWidth = $destWidth * $fill;
        $boundingHeight = $destHeight * $fill;

        if ($boundingWidth === 0.0 || $boundingHeight === 0.0 || $srcWidth === 0 || $srcHeight === 0) {
            return;
        }

        $boundingRatio = $boundingWidth / $boundingHeight;
        $photoRatio = $srcWidth / $srcHeight;

        if ($photoRatio > $boundingRatio) {
            $scaleWith = (int) $boundingWidth;
            $scaleHeight = (int) ($boundingWidth / $photoRatio);
        } else {
            $scaleWith = (int) ($boundingHeight * $photoRatio);
            $scaleHeight = (int) $boundingHeight;
        }
        $offsetX = (int) (($destWidth - $scaleWith) / 2);
        $offsetY = (int) (($destHeight - $scaleHeight) / 2);

        $scaled = imagescale($src, $scaleWith, $scaleHeight);
        imagecopymerge($dest, $scaled, $offsetX, $offsetY, 0, 0, $scaleWith, $scaleHeight, (int) ($alpha * 100));
    }

    // Gradient function taken from:
    // http://www.supportduweb.com/scripts_tutoriaux-code-source-41-gd-faire-un-degrade-en-php-gd-fonction-degrade-imagerie.html
    private static function degrade(&$img, string $direction, array $color1, array $color2)
    {
        if ($direction === 'h') {
            $size = imagesx($img);
            $sizeinv = imagesy($img);
        } else {
            $size = imagesy($img);
            $sizeinv = imagesx($img);
        }
        $diffs = [
            (($color2[0] - $color1[0]) / $size),
            (($color2[1] - $color1[1]) / $size),
            (($color2[2] - $color1[2]) / $size),
        ];
        for ($i = 0; $i < $size; ++$i) {
            $r = $color1[0] + ($diffs[0] * $i);
            $g = $color1[1] + ($diffs[1] * $i);
            $b = $color1[2] + ($diffs[2] * $i);
            if ($direction === 'h') {
                imageline($img, $i, 0, $i, $sizeinv, imagecolorallocate($img, (int) $r, (int) $g, (int) $b));
            } else {
                imageline($img, 0, $i, $sizeinv, $i, imagecolorallocate($img, (int) $r, (int) $g, (int) $b));
            }
        }
    }
}
