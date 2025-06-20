<?php
require_once "vendor/autoload.php";

$fsource = "benchmark-img.jpg";
$fdest_imagick = "test-imagick.jpg";
$fdest_gd = "test-gd.jpg";
$fdest_vip = "test-vip.jpg";

$w = 640;
$h = 640;

$results = [];

// Print library versions

echo "GD version:      ";
if (function_exists('gd_info')) {
    $gd = gd_info();
    echo isset($gd['GD Version']) ? $gd['GD Version'] : 'Unknown';
} else {
    echo "GD not available";
}
echo "\n";

echo "Imagick version: ";
if (class_exists('Imagick')) {
    $imv = new Imagick();
    echo $imv->getVersion()['versionString'] ?? 'Unknown';
} else {
    echo "Imagick not available";
}
echo "\n";

echo "VIPS version:    ";
if (class_exists('Jcupitt\\Vips\\Image')) {
    if (defined('Jcupitt\\Vips\\FFI_VERSION')) {
        echo constant('Jcupitt\\Vips\\FFI_VERSION');
    } elseif (class_exists('Jcupitt\\Vips\\Config') && method_exists('Jcupitt\\Vips\\Config', 'getVersion')) {
        echo Jcupitt\Vips\Config::getVersion();
    } elseif (method_exists('Jcupitt\\Vips\\Image', 'getVersion')) {
        echo Jcupitt\Vips\Image::getVersion();
    } else {
        echo "Unknown";
    }
} else {
    echo "VIPS not available";
}
echo "\n\n";

// ~~~~~~~~~~~~~~~~~ ImageMagick ~~~~~~~~~~~~~~~~~~~~~
$start_time = microtime(true);
$mem_start = memory_get_usage();
$im = new imagick($fsource);
$im->setImageCompression(Imagick::COMPRESSION_JPEG);
$im->setImageCompressionQuality(90);
$im->stripImage();
$im->thumbnailImage($w, 0);
$im->writeImage($fdest_imagick);
$mem_end = memory_get_usage();
$end_time = microtime(true);
$imagick_time = $end_time - $start_time;
$imagick_size = file_exists($fdest_imagick) ? filesize($fdest_imagick) : 0;
$imagick_mem = $mem_end - $mem_start;
$results[] = [
    'Library' => 'ImageMagick',
    'Time (s)' => number_format($imagick_time, 6),
    'Size (KB)' => $imagick_size ? round($imagick_size / 1024) : 'N/A',
    'Memory (KB)' => round($imagick_mem / 1024)
];

// ~~~~~~~~~~~~~~~~~~ GD php library ~~~~~~~~~~~~~~~~~~~~
$start_time = microtime(true);
$mem_start = memory_get_usage();
list($width, $height) = getimagesize($fsource);
$src = imagecreatefromjpeg($fsource);
$dst = imagecreatetruecolor($w, $h);
imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
imagejpeg($dst, $fdest_gd, 90);
imagedestroy($src);
imagedestroy($dst);
$mem_end = memory_get_usage();
$end_time = microtime(true);
$gd_time = $end_time - $start_time;
$gd_size = file_exists($fdest_gd) ? filesize($fdest_gd) : 0;
$gd_mem = $mem_end - $mem_start;
$results[] = [
    'Library' => 'GD',
    'Time (s)' => number_format($gd_time, 6),
    'Size (KB)' => $gd_size ? round($gd_size / 1024) : 'N/A',
    'Memory (KB)' => round($gd_mem / 1024)
];

// ~~~~~~~~~~~~~~~~~~ VIPS php library ~~~~~~~~~~~~~~~~~~~~
if (class_exists('Jcupitt\Vips\Image')) {
    // Disable VIPS cache to ensure consistent results
    Jcupitt\Vips\Config::cacheSetMax(0);

    $start_time = microtime(true);
    $mem_start = memory_get_usage();
    $image = Jcupitt\Vips\Image::newFromFile($fsource);
    $thumb = $image->thumbnail_image($w, ["height" => $h]);
    $thumb->jpegsave($fdest_vip, ["Q" => 90]);
    $mem_end = memory_get_usage();
    $end_time = microtime(true);
    $vips_time = $end_time - $start_time;
    $vips_size = file_exists($fdest_vip) ? filesize($fdest_vip) : 0;
    $vips_mem = $mem_end - $mem_start;
    $results[] = [
        'Library' => 'VIPS',
        'Time (s)' => number_format($vips_time, 6),
        'Size (KB)' => $vips_size ? round($vips_size / 1024) : 'N/A',
        'Memory (KB)' => round($vips_mem / 1024)
    ];

    // Delete the memory used by the image object
    unset($image);
    unset($thumb);

    // ~~~~~~~~~~~~~ VIPS quality 85 ~~~~~~~~~~~~~~
    // Measure memory usage for VIPS quality 85
    $fdest_vip85 = "test-vip89.jpg";
    $start_time_85 = microtime(true);
    $mem_start_85 = memory_get_usage();
    $image = Jcupitt\Vips\Image::newFromFile($fsource);
    $thumb = $image->thumbnail_image($w, ["height" => $h]);
    $thumb->jpegsave($fdest_vip85, ["Q" => 85]);
    $mem_end_85 = memory_get_usage();
    $end_time_85 = microtime(true);
    $vips85_time = $end_time_85 - $start_time_85;
    $vips85_size = file_exists($fdest_vip85) ? filesize($fdest_vip85) : 0;
    $vips85_mem = $mem_end_85 - $mem_start_85;
    $results[] = [
        'Library' => 'VIPS89',
        'Time (s)' => number_format($vips85_time, 6),
        'Size (KB)' => $vips85_size ? round($vips85_size / 1024) : 'N/A',
        'Memory (KB)' => round($vips85_mem / 1024)
    ];

    // Delete the memory used by the image object
    unset($image);
    unset($thumb);

    // ~~~~~~~~~~~~~ VIPS quality 85 ~~~~~~~~~~~~~~
    // Measure memory usage for VIPS quality 85
    $fdest_vip85 = "test-vip75.jpg";
    $start_time_85 = microtime(true);
    $mem_start_85 = memory_get_usage();
    $image = Jcupitt\Vips\Image::newFromFile($fsource);
    $thumb = $image->thumbnail_image($w, ["height" => $h]);
    $thumb->jpegsave($fdest_vip85, ["Q" => 75]);
    $mem_end_85 = memory_get_usage();
    $end_time_85 = microtime(true);
    $vips85_time = $end_time_85 - $start_time_85;
    $vips85_size = file_exists($fdest_vip85) ? filesize($fdest_vip85) : 0;
    $vips85_mem = $mem_end_85 - $mem_start_85;
    $results[] = [
        'Library' => 'VIPS Q=75 (default)',
        'Time (s)' => number_format($vips85_time, 6),
        'Size (KB)' => $vips85_size ? round($vips85_size / 1024) : 'N/A',
        'Memory (KB)' => round($vips85_mem / 1024)
    ];
} else {
    $results[] = [
        'Library' => 'VIPS',
        'Time (s)' => 'N/A',
        'Size (KB)' => 'N/A',
        'Memory (KB)' => 'N/A',
    ];
}

// Print results as a table in the terminal

function printTable($data) {
    if (empty($data)) return;
    $headers = array_keys($data[0]);
    $lengths = [];
    foreach ($headers as $header) {
        $lengths[$header] = strlen($header);
    }
    foreach ($data as $row) {
        foreach ($row as $key => $value) {
            $lengths[$key] = max($lengths[$key], strlen((string)$value));
        }
    }
    // Print header
    foreach ($headers as $header) {
        printf("| % -" . $lengths[$header] . "s ", $header);
    }
    echo "|\n";
    // Print separator
    foreach ($headers as $header) {
        echo "+-" . str_repeat('-', $lengths[$header]) . "-";
    }
    echo "+\n";
    // Print rows
    foreach ($data as $row) {
        foreach ($headers as $header) {
            printf("| % -" . $lengths[$header] . "s ", $row[$header]);
        }
        echo "|\n";
    }
}

printTable($results);
