#FROM alpine:3.14
FROM harbor.vimpelcom.ru/dockerhub/library/alpine:3.14
RUN apk --no-cache add \
  curl \
  openssh-client \
  bash \
  git \
  net-tools \
  vim \
  nginx \
  php8 \
  php8-ctype \
  php8-curl \
  php8-dom \
  php8-fpm \
  php8-gd \
  php8-intl \
  php8-json \
  php8-mbstring \
  php8-mysqli \
  php8-pdo_mysql \
  php8-opcache \
  php8-openssl \
  php8-phar \
  php8-session \
  php8-xml \
  php8-xmlreader \
  php8-xmlwriter \
  php8-tokenizer \
  php8-zlib \
  php8-iconv \
  php8-fileinfo \
  php8-simplexml \
  php8-zip \
  supervisor
  #  && rm /etc/nginx/conf.d/default.conf #не нужно в 3.14

# Create symlink so programs depending on `php` still function
RUN ln -s /usr/bin/php8 /usr/bin/php

# Configure nginx
COPY docker-data/service-config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker-data/service-config/fpm-pool.conf /etc/php8/php-fpm.d/www.conf
COPY docker-data/service-config/php.ini /etc/php8/conf.d/custom.ini

# Configure supervisord
COPY docker-data/service-config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Add preloaded curl certificate. See php.ini curl.cainfo
COPY docker-data/cert/cacert.pem /etc/ssl/cacert.pem

# Setup document root
RUN mkdir -p /var/www/dpl

RUN mkdir -p /.composer/cache/vcs && \
  mkdir -p /.composer/cache/repo && \
  mkdir -p /.composer/cache/files && \
  chown -R nobody.nobody /.composer

# Make sure files/folders needed by the processes are accessible when they run under the nobody user
RUN chown -R nobody.nobody /var/www/dpl && \
  chown -R nobody.nobody /run && \
  chmod o+w /run && \
  chmod o+w /var/log/nginx && \
  chmod o+w /var/lib/nginx && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

COPY --from=harbor.vimpelcom.ru/dockerhub/library/composer:latest /usr/bin/composer /usr/local/bin/composer
#COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Switch to use a non-root user from here on
USER nobody

# change path to application dir
WORKDIR /var/www/dpl

# Set github oauth keys for composer
ENV COMPOSER_AUTH='{"github-oauth": {"github.com": "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"}}'

#RUN git clone https://github.com/cusodede/dpl.git .
RUN git clone git@git.vimpelcom.ru:1122/products/dsp/dpl-main.git .

# Add local application configurations
COPY docker-data/web-config /var/www/dpl/config/local/

RUN composer install --no-dev

RUN chmod o+wx /var/www/dpl/config/db.php && \
  chmod 777 /var/www/dpl/runtime && \
  mkdir -p /var/www/dpl/web/assets && \
  chmod 777 /var/www/dpl/web/assets && \
  chmod 755 /var/www/dpl/yii

RUN php yii migrate --migrationPath=@vendor/pozitronik/yii2-users-options/migrations --interactive=0 && \
  php yii migrate --migrationPath=@vendor/pozitronik/yii2-options/migrations --interactive=0 && \
  php yii migrate --migrationPath=@vendor/pozitronik/yii2-exceptionslogger/migrations --interactive=0 && \
  php yii migrate --migrationPath=@vendor/pozitronik/yii2-filestorage/migrations --interactive=0 && \
  php yii migrate --interactive=0

RUN php yii service/init && \
  php yii service/init-config-permissions && \
  php yii service/init-controllers-permissions

USER root

RUN chown -R nobody.nobody /var/www/dpl && \
  chmod o+wx /var/www/dpl/config/local/web.php

USER nobody

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping