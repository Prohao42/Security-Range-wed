FROM php:7.3-apache

# ---- 1. 系统依赖（仅 gd 所需图像库）----
# 基础镜像为 Debian bullseye（已 EOL），官方源与 security 套件均已下架（404），
# 故改用归档镜像源，并关闭 Release 文件的 Valid-Until 校验
# 备选归档源：archive.debian.org/debian（国外较慢）
RUN set -eux; \
    echo 'deb https://mirrors.aliyun.com/debian-archive/debian bullseye main' > /etc/apt/sources.list; \
    echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpng-dev libjpeg-dev libfreetype6-dev; \
    rm -rf /var/lib/apt/lists/*

# ---- 2. PHP 扩展（复刻 phpStudy 启用项）----
RUN set -eux; \
    docker-php-ext-configure gd --with-jpeg-dir=/usr --with-freetype-dir=/usr; \
    docker-php-ext-install -j"$(nproc)" mysqli pdo_mysql gd

# ---- 3. Apache 模块 ----
RUN a2enmod rewrite headers deflate filter ssl

# ---- 4. 复刻配置 ----
COPY docker/php/zz-zhazhasu.ini      /usr/local/etc/php/conf.d/zz-zhazhasu.ini
COPY docker/apache/000-zhazhasu.conf /etc/apache2/sites-available/000-default.conf

# ---- 5. 项目代码 ----
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
