## Benchmarking image thumbnail generation in PHP 8
#### Original benchmark script from https://robertvicol.com/tech/imagemagick-vs-gd-benchmark-resize-image-in-php-script/

## Requirements

- Docker
- Docker Compose

This benchmark is ready to run in a container with PHP 8.3 and the extensions GD, Imagick, VIPS, and blake3.

## How to run the benchmark with Docker

1. Build the image:

   ```sh
   docker compose build
   ```

2. Run the benchmark:

   ```sh
   docker compose up
   ```

This will execute the `benchmark.php` script in a clean environment with PHP 8.3 and all required extensions.

## Results

They say on GD vs Imagick, Imagick always is faster. Not on my machine. CPU i7-4702MQ.

Benchmark results: (image quality set to 90)

- ImageMagick took: 0.37198996543884 seconds. 103 KB.
- GD took: 0.29470610618591 seconds. 164 KB.
- VIPS took: 0.083019018173218 seconds. 53 KB.

Example images:

Original

[<img src="benchmark-img.jpg" width="640"/>](benchmark-img.jpg)

ImageMagick

![Test Imagick](test-imagick.jpg)

GD

![Test GD](test-gd.jpg)

VIPS

![Test VIPS](test-vip.jpg)

## Donate
https://ko-fi.com/cypherbits

Monero address:
`4BCveGZaPM7FejGkhFyHgtjVXZw52RrYxKs7znZdmnWLfB3xDKAW6SkYZPpNhqBvJA8crE8Tug8y7hx8U9KAmq83PwLtVLe`