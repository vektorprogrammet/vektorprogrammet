# Dockerfile for making ubuntu:18.04 into a php/node environment for running and developing vektorprogrammet
# Usage: See docker.md

FROM ubuntu:18.04
RUN mkdir /app
WORKDIR /app
EXPOSE 8000

# Tell packages to not ask for user input
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
    && apt-get install -y php7.2 php7.2-mbstring php7.2-sqlite php7.2-gd php7.2-curl php7.2-xml nodejs npm git zip unzip \
    && echo "PHP extensions: " \
    && php -m \
    && echo "PHP .ini files: " \
    && php -i \
    # Make some changes to php.ini file. Many extensions are already enabled in other files
    #sed -i 's/;extension=mbstring/extension=mbstring/' /etc/php/7.2/cli/php.ini
    #sed -i 's/;extension=pdo_sqlite/extension=pdo_sqlite/' /etc/php/7.2/cli/php.ini
    #sed -i 's/;extension=gd2/extension=gd2/' /etc/php/7.2/cli/php.ini
    #sed -i 's/;extension=curl/extension=curl/' /etc/php/7.2/cli/php.ini
    #sed -i 's/;extension=xml/extension=xml/' /etc/php/7.2/cli/php.ini
    && sed -i 's/display_errors = Off/display_errors = On/' /etc/php/7.2/cli/php.ini \
    && sed -i 's/display_startup_errors = Off/display_startup_errors = On/' /etc/php/7.2/cli/php.ini

# This is what we /would/ do if we didn't mount the entire folder into the container
# COPY . .
# RUN npm run setup

# Default command for running, will complain about no package.json if app/ isn't mounted
CMD npm start
