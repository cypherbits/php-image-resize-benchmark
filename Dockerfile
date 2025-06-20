FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive
ENV LC_ALL=C.UTF-8

# Update and install basic dependencies
RUN apt-get update -y && apt-get dist-upgrade -y && apt-get install -y \
    software-properties-common wget unzip nano

# Add PHP repository
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php

# Install PHP 8.3 and required extensions
RUN apt-get update -y && apt-get install -y \
    php8.3 php8.3-cli php8.3-gd php8.3-imagick php8.3-vips \
    libvips42

# Install libjpeg-turbo for optimized JPEG processing
RUN apt-get install -y libjpeg-turbo8 libjpeg-turbo-progs

# Install build dependencies for MozJPEG
#RUN apt-get update -y && apt-get install -y \
#    build-essential cmake git autoconf automake libtool pkg-config nasm
# Install libpng-dev for MozJPEG build
#RUN apt-get install -y libpng-dev
## Build and install MozJPEG
#RUN cd /tmp && git clone --depth 1 https://github.com/mozilla/mozjpeg.git \
#    && cd mozjpeg \
#    && mkdir build && cd build \
#    && cmake -G"Unix Makefiles" -DCMAKE_BUILD_TYPE=Release .. \
#    && make && make install \
#    && ln -s /opt/mozjpeg/bin/cjpeg /usr/bin/mozjpeg-cjpeg \
#    && ln -s /opt/mozjpeg/bin/djpeg /usr/bin/mozjpeg-djpeg

# Install blake3 extension
RUN cd /tmp && wget https://github.com/cypherbits/php-blake3/releases/download/v1.0-php8.3/blake3.zip \
    && unzip blake3.zip \
    && cp /tmp/blake3.so $(php -r 'echo ini_get("extension_dir");')/blake3.so

# Enable blake3 extension in CLI
RUN echo "extension=blake3.so" >> /etc/php/8.3/cli/php.ini

# Copy composer.json and composer.lock first to leverage Docker cache
COPY . /app
WORKDIR /app

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --prefer-dist

CMD ["php", "benchmark.php"]
