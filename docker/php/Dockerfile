FROM php:8.3-fpm-alpine

# Useful PHP extension installer image, copy binary into your container
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# APK stands for Alpine Linux package keeper (manager). One can use the apk command to delete, install, upgrade, or list software
# An access control list (ACL) contains rules that grant or deny access to certain digital environments.
# Description: FASTCgi (fcgi) is a language independent, high performant extension to CGI. https://archlinux.org/packages/extra/x86_64/fcgi/
# The file command is used to determine the type of a file
# The gettext program translates a natural language message into the user's language
RUN apk add --no-cache \
		acl \
        bash \
        fcgi \
		file \
		gettext \
		git \
	;

# https://docs.moodle.org/311/en/admin/environment/php_extension/intl
# https://www.educba.com/php-zip-files/
# The APCu extension adds object caching functions to PHP.
# OPcache is a type of caching system that saves precompiled script bytecode in a server’s memory called a cache, so each time a user visits a web page, it loads faster.
RUN set -eux; \
    install-php-extensions \
    	intl \
    	zip \
    	apcu \
		opcache \
        pdo pdo_mysql \
    ;

# Copy custom php.ini
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Install Xdebug and copy configuration
RUN set -eux; \
	install-php-extensions xdebug
COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Set permissions for the uploads directory
RUN mkdir -p /var/www/html/public/uploads/documents && \
    chown -R www-data:www-data /var/www/html/public/uploads/documents && \
    chmod -R 777 /var/www/html/public/uploads/documents

# Set working directory
WORKDIR /var/www/html