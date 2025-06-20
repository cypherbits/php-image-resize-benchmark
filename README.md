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

They say on GD vs Imagick, Imagick always is faster. Not on my machine. 

Tested on CPU i7-4702MQ
Tested on CPU i7-13700K

Benchmark results for i7-13700K on quality (Q) 90:

| Library             | Time (s)  | Size (KB) |
|---------------------|-----------|-----------|
| ImageMagick         | 0.129705  | 100       |
| GD                  | 0.141723  | 221       |
| VIPS                | 0.103115  | 170       |
| VIPS Q=89           | 0.089695  | 75        |
| VIPS Q=75 (default) | 0.086163  | 52        |

VIPS concurrency is set to 1 using Docker `ENV VIPS_CONCURRENCY=1`.