<?php
/**
 * Based on https://framagit.org/framasoft/framabin/blob/master/lib/vizhash_gd_zero.php
 * SPDX-License-Identifier: Zlib.
 */

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\VizHash;

class VizHash
{
    /**
     * This is the main function for creating a full visual hash, including a "random" background, a photo
     * in the middle and a text at the bottom.
     *
     * @param string  $hash        The input for the hash background
     * @param string  $description The description shown below the photo
     * @param string  $photoData   The photo to include
     * @param int     $size        The width/height of the result in pixels
     * @param ?string $watermark   The watermark text to include
     * @param string  $fontFile    A path to a .ttf file
     * @param int     $jpegQuality The quality of the resulting jpeg file
     * @param bool    $grayScale   If true the background will be done in grayscale
     *
     * @return string A jpeg image
     */
    public static function create(string $hash, string $description, string $photoData, int $size, ?string $watermark, string $fontFile, int $jpegQuality, bool $grayScale = false): string
    {
        $p = $size / 100;

        // Generate the background
        $background = VizHash::generateBackground($hash, $size, $size);

        if ($grayScale) {
            imagefilter($background, IMG_FILTER_GRAYSCALE);
        }

        // Blend in the photo
        $photo = @imagecreatefromstring($photoData);
        if ($photo === false) {
            throw new \RuntimeException(\error_get_last()['message']);
        }
        imagefilter($photo, IMG_FILTER_GRAYSCALE);
        imagefilter($photo, IMG_FILTER_CONTRAST, -30);
        VizHash::blendPhoto($background, $photo, [5 * $p, 5 * $p, 20 * $p, 5], 0.7);
        imagedestroy($photo);
        if ($watermark !== null) {
            self::addWatermark($background, $watermark, $fontFile);
        }

        self::addDescription($background, $description, 15 * $p, 2 * $p, $fontFile, 0.45);

        $data = self::imageToJpeg($background, $jpegQuality);
        imagedestroy($background);

        return $data;
    }

    public static function imageToJpeg($image, int $quality): string
    {
        ob_start();
        imagejpeg($image, null, $quality);
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    /**
     * Adds a watermark text across the $dest image.
     */
    public static function addWatermark(&$dest, string $text, string $fontFile)
    {
        // XXX: not generalized, assumes a square image and a minimum text length
        $maxWidth = sqrt(imagesx($dest) ** 2 + imagesy($dest) ** 2) * 0.92;
        $padding = (int) ($maxWidth / 80);

        $getBoundingBox = function ($size, $fontFile, $text) {
            $values = @imagettfbbox($size, 0, $fontFile, $text);
            if ($values === false) {
                return [0, 0];
            }

            return [$values[2] - $values[0], $values[3] - $values[5]];
        };

        // Select the best font size for the bounding box
        $selectedSize = 0;
        for ($i = 0; $i < $maxWidth; ++$i) {
            [$w, $h] = $getBoundingBox($i, $fontFile, $text);
            if ($w >= ($maxWidth - $padding * 2)) {
                break;
            }
            $selectedSize = $i;
        }

        $temp = imagecreatetruecolor(imagesx($dest), imagesy($dest));
        imagefill($temp, 0, 0, imagecolorallocatealpha($temp, 0, 0, 0, 127));

        // Add some kind of text border
        $white = imagecolorallocatealpha($temp, 255, 255, 255, 0);
        $temp = self::textWithBorder($temp, $selectedSize, 45, (int) ($selectedSize / 2) + $padding, imagesy($dest), $white, $fontFile, $text);

        // Three rows of text
        imagecopy($dest, $temp, 0, 0, 0, 0, imagesx($dest), imagesy($dest));
        imagecopy($dest, $temp, imagesx($dest) / 4, imagesy($dest) / 4, 0, 0, imagesx($dest), imagesy($dest));
        imagecopy($dest, $temp, -imagesx($dest) / 4, -imagesy($dest) / 4, 0, 0, imagesx($dest), imagesy($dest));
    }

    /**
     * @psalm-suppress TooFewArguments
     */
    private static function imagefilledpolygon($image, array $points, int $color): bool
    {
        if (\PHP_VERSION_ID >= 80000) {
            /* @phpstan-ignore-next-line */
            return imagefilledpolygon($image, $points, $color);
        } else {
            return imagefilledpolygon($image, $points, count($points) / 2, $color);
        }
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
        $getInt = function () use ($values, &$valuesIndex): int {
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
        $getY = function () use ($getInt, $height): int {
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
                    self::imagefilledpolygon($image, $points, $color);
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
            $r = ((int) ($r0 + $getInt() / 25)) % 256;
            $g = ((int) ($g0 + $getInt() / 25)) % 256;
            $b = ((int) ($b0 + $getInt() / 25)) % 256;
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
     * Draws text at the bottom of $dest.
     * The text size will be automatically adjusted to fit the whole box and the text will be
     * horizontally and vertically centered.
     *
     * @param mixed  $dest      The GDImage to draw to
     * @param string $text      The text to draw
     * @param int    $maxHeight The max height of the text
     * @param int    $padding   The padding around the text
     * @param string $fontFile  The font file to use
     * @param float  $alpha     How transparent the text background should be: 0=fully transparent, 1=fully opaque
     */
    public static function addDescription(&$dest, string $text, int $maxHeight, int $padding, string $fontFile, float $alpha)
    {
        $maxWidth = imagesx($dest);

        $getBoundingBox = function ($size, $fontFile, $text) {
            $values = @imagettfbbox($size, 0, $fontFile, $text);
            if ($values === false) {
                return [0, 0];
            }

            return [$values[2] - $values[0], $values[3] - $values[5]];
        };

        // Select the best font size for the bounding box
        $selectedSize = 0;
        for ($i = 0; $i < $maxHeight; ++$i) {
            [$w, $h] = $getBoundingBox($i, $fontFile, $text);
            if ($w >= ($maxWidth - $padding * 2) || $h >= ($maxHeight - $padding * 2)) {
                break;
            }
            $selectedSize = $i;
        }

        // Center at the bottom
        [$w, $h] = $getBoundingBox($selectedSize, $fontFile, $text);
        $x = (int) (($maxWidth - $w) / 2);
        $y = imagesy($dest) - (int) (($maxHeight - $h) / 2);

        // XXX: There is no API to figure out the baseline offset, guess from the size for now
        $small = $getBoundingBox($selectedSize, $fontFile, 'ABCI')[1];
        $baseline = $y - ($h - $small);

        // Add a semi transparent background box
        $backgroundBox = imagecreatetruecolor(imagesx($dest), $maxHeight);
        imagefill($backgroundBox, 0, 0, imagecolorallocatealpha($backgroundBox, 0, 0, 0, 127));
        imagecopymerge($dest, $backgroundBox, 0, imagesy($dest) - $maxHeight, 0, 0,
            imagesx($backgroundBox), imagesy($backgroundBox), (int) ($alpha * 100));

        // Finally, draw the text in white on top
        $relBaseline = $baseline - imagesy($dest) + $maxHeight;
        $temp = imagecreatetruecolor(imagesx($dest), $maxHeight);
        imagefill($temp, 0, 0, imagecolorallocatealpha($temp, 0, 0, 0, 127));

        $white = imagecolorallocatealpha($temp, 255, 255, 255, 0);
        $temp = self::textWithBorder($temp, $selectedSize, 0, $x, $relBaseline, $white, $fontFile, $text);

        imagecopy($dest, $temp, 0, imagesy($dest) - $maxHeight, 0, 0, imagesx($dest), imagesy($dest));
    }

    /**
     * Like imagettftext(), but draws a black-ish border around the text.
     */
    public static function textWithBorder($image, $size, $angle, $x, $y, $color, $fontfile, $text)
    {
        $black = imagecolorallocatealpha($image, 0, 0, 0, 0);
        $res = @imagettftext($image, $size, $angle, $x, $y, $black, $fontfile, $text);
        if ($res === false) {
            throw new \RuntimeException(\error_get_last()['message']);
        }

        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        for ($i = 0; $i < 4; ++$i) {
            for ($j = 0; $j < max(intdiv($size, 30), 1); ++$j) {
                imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
            }
            $image = imagerotate($image, 90, $transparent);
        }
        imagefilter($image, IMG_FILTER_BRIGHTNESS, 20);

        $res = @imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $text);
        if ($res === false) {
            throw new \RuntimeException(\error_get_last()['message']);
        }

        return $image;
    }

    /**
     * Blend $src on top of $dest, centered, with variable padding and alpha.
     *
     * @param mixed $dest    The image to blend on top of
     * @param mixed $src     The image to blend onto $dest
     * @param array $padding array with padding values (top, right, bottom, left)
     * @param float $alpha   How transparent $src should be: 0=fully transparent, 1=fully opaque
     */
    public static function blendPhoto(&$dest, &$src, array $padding, float $alpha)
    {
        $destWidth = imagesx($dest);
        $destHeight = imagesy($dest);
        $srcWidth = imagesx($src);
        $srcHeight = imagesy($src);
        [$top, $right, $bottom, $left] = $padding;
        $boundingWidth = $destWidth - ($left + $right);
        $boundingHeight = $destHeight - ($top + $bottom);

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
        $offsetX = (int) (($destWidth - $left - $right - $scaleWith) / 2) + $left;
        $offsetY = (int) (($destHeight - $top - $bottom - $scaleHeight) / 2) + $top;

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
