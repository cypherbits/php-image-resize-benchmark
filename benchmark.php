<?php
require_once "vendor/autoload.php";

$fsource = "benchmark-img.jpg";
$fdest_imagick = "test-imagick.jpg";
$fdest_gd = "test-gd.jpg";
$fdest_vip = "test-vip.jpg";

$w = 640;
$h = 640;

// ~~~~~~~~~~~~~~~~~ ImageMagick ~~~~~~~~~~~~~~~~~~~~~

$start_time = microtime(true);

$im = new imagick( $fsource );
$im->setImageCompression(Imagick::COMPRESSION_JPEG);
$im->setImageCompressionQuality(90);
$im->stripImage();
$im->thumbnailImage($w, 0);
//$im->resizeImage($w,$h,Imagick::FILTER_LANCZOS,1);
$im->writeImage( $fdest_imagick );

$end_time = microtime(true);

echo ($end_time-$start_time) . " sec";
echo "<br />";
echo "<img src='$fdest_imagick'>";

echo "<hr />";

// ~~~~~~~~~~~~~~~~~~ GD php library ~~~~~~~~~~~~~~~~~~~~

$start_time = microtime(true);


list($width, $height) = getimagesize( $fsource );
$r = $width / $height;

if ($w/$h > $r) {
    $newwidth = $h*$r;
    $newheight = $h;
} else {
    $newheight = $w/$r;
    $newwidth = $w;
}

$src = imagecreatefromjpeg($fsource);
$dst = imagecreatetruecolor($newwidth, $newheight);
imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

imagejpeg($dst, $fdest_gd, 90 );

$end_time = microtime(true);

echo ($end_time-$start_time) . " sec";
echo "<br />";
echo "<img src='$fdest_gd'>";

// ~~~~~~~~~~~~~~~~~~ VIPs php library ~~~~~~~~~~~~~~~~~~~~

$start_time = microtime(true);

$im = Jcupitt\Vips\Image::thumbnail($fsource, $w, ['height' => $h]);
$im->writeToFile($fdest_vip, ["Q" => 90]);


$end_time = microtime(true);

echo ($end_time-$start_time) . " sec";
echo "<br />";
echo "<img src='$fdest_vip'>";
