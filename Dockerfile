FROM php:8.3.0-fpm-alpine

# Update the package lists and install git
RUN apk update && apk add --no-cache git

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add Composer to the PATH
ENV PATH="$PATH:/usr/local/bin"
