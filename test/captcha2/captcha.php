<?php

$captcha = new captcha();
$captcha->CreateImage();

class captcha
{
    public $width = 150;
    public $height = 50;
    public $font_path = 'fonts/';
    public $minWordLength = 5;
    public $maxWordLength = 5;
    public $backgroundColor = array(255, 255, 255);
    public $colors = array(array(27,78,181),array(22,163,35),array(214,36,7));
    public $shadowColor = null;
    public $fonts = array(
        'Antykwa' => array('spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => 'AntykwaBold.ttf'),
        'Candice' => array('spacing' => -1.5,'minSize' => 28, 'maxSize' => 31, 'font' => 'Candice.ttf'),
        'DingDong' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => 'Ding-DongDaddyO.ttf'),
        'Duality' => array('spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => 'Duality.ttf'),
        'Heineken' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 34, 'font' => 'Heineken.ttf'),
        'Jura' => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => 'Jura.ttf'),
        'StayPuft' => array('spacing' => -1.5,'minSize' => 28, 'maxSize' => 32, 'font' => 'StayPuft.ttf'),
        'Times' => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => 'TimesNewRomanBold.ttf'),
        'VeraSans' => array('spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => 'VeraSansBold.ttf'),
    );
    public $Yperiod = 12;
    public $Yamplitude = 14;
    public $Xperiod = 11;
    public $Xamplitude = 5;
    public $maxRotation = 8;
    public $scale = 2;
    public $blur = false;
    public $imageFormat = 'png';
    public $im;

    public function CreateImage()
    {
        $ini = microtime(true);
        $this->ImageAllocate();
        $text = $this->GetCaptchaText();
        $fontcfg = $this->fonts[array_rand($this->fonts)];
        $this->WriteText($text, $fontcfg);
        $this->WaveImage();
        if ($this->blur && function_exists('imagefilter')) {
            imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $this->ReduceImage();
        $this->WriteImage();
        $this->Cleanup();
    }

    public function ImageAllocate()
    {
        $this->im = imagecreatetruecolor($this->width * $this->scale, $this->height * $this->scale);
        $this->GdBgColor = imagecolorallocate($this->im, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
        imagefilledrectangle($this->im, 0, 0, $this->width * $this->scale, $this->height * $this->scale, $this->GdBgColor);
        $color = $this->colors[mt_rand(0, sizeof($this->colors) - 1)];
        $this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
        if (!empty($this->shadowColor) && is_array($this->shadowColor) && sizeof($this->shadowColor) >= 3) {
            $this->GdShadowColor = imagecolorallocate($this->im, $this->shadowColor[0], $this->shadowColor[1], $this->shadowColor[2]);
        }
    }

    public function GetCaptchaText()
    {
        $length = rand($this->minWordLength, $this->maxWordLength);
        $words = 'abcdefghijlmnopqrstvwyz';
        $vocals = 'aeiou';

        $text = '';
        $vocal = rand(0, 1);
        for ($i = 0; $i < $length; ++$i) {
            if ($vocal) {
                $text .= substr($vocals, mt_rand(0, 4), 1);
            } else {
                $text .= substr($words, mt_rand(0, 22), 1);
            }
            $vocal = !$vocal;
        }

        return $text;
    }

    public function WriteText($text, $fontcfg = array())
    {
        if (empty($fontcfg)) {
            $fontcfg = $this->fonts[array_rand($this->fonts)];
        }
        $fontfile = $this->font_path.$fontcfg['font'];

        $lettersMissing = $this->maxWordLength - strlen($text);
        $fontSizefactor = 1 + ($lettersMissing * 0.09);

        $x = 20 * $this->scale;
        $y = round(($this->height * 27 / 40) * $this->scale);
        $length = strlen($text);
        for ($i = 0; $i < $length; ++$i) {
            $degree = rand($this->maxRotation * -1, $this->maxRotation);
            $fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize']) * $this->scale * $fontSizefactor;
            $letter = substr($text, $i, 1);
            if ($this->shadowColor) {
                $coords = imagettftext($this->im, $fontsize, $degree,
                    $x + $this->scale, $y + $this->scale,
                    $this->GdShadowColor, $fontfile, $letter);
            }
            $coords = imagettftext($this->im, $fontsize, $degree,
                $x, $y,
                $this->GdFgColor, $fontfile, $letter);
            $x += ($coords[2] - $x) + ($fontcfg['spacing'] * $this->scale);
        }
    }

    public function WaveImage()
    {
        $xp = $this->scale * $this->Xperiod * rand(1, 3);
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->width * $this->scale); ++$i) {
            imagecopy($this->im, $this->im, $i - 1, sin($k + $i / $xp) * ($this->scale * $this->Xamplitude), $i, 0, 1, $this->height * $this->scale);
        }
        $k = rand(0, 100);
        $yp = $this->scale * $this->Yperiod * rand(1, 2);
        for ($i = 0; $i < ($this->height * $this->scale); ++$i) {
            imagecopy($this->im, $this->im, sin($k + $i / $yp) * ($this->scale * $this->Yamplitude), $i - 1, 0, $i, $this->width * $this->scale, 1);
        }
    }

    public function ReduceImage()
    {
        $imResampled = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($imResampled, $this->im, 0, 0, 0, 0, $this->width, $this->height, $this->width * $this->scale, $this->height * $this->scale);
        imagedestroy($this->im);
        $this->im = $imResampled;
    }

    public function WriteImage()
    {
        if ($this->imageFormat == 'png' && function_exists('imagepng')) {
            header('Content-type: image/png');
            imagepng($this->im);
        } else {
            header('Content-type: image/jpeg');
            imagejpeg($this->im, null, 80);
        }
    }

    public function Cleanup()
    {
        imagedestroy($this->im);
    }
}
