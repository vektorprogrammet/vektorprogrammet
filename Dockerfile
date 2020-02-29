# Dockerfile for å kjøre vektorprogrammets binaries i ubuntu 18.04, men likevel hente all php-kildekode fra host-systemet
# Bruk:
#

FROM ubuntu:18.04
RUN mkdir /app
WORKDIR /app
EXPOSE 8000

# Sånn at kommandoer ikke ber om bruker-input
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update
RUN apt-get install -y php7.2 php7.2-mbstring php7.2-sqlite php7.2-gd php7.2-curl php7.2-xml nodejs npm git zip unzip

RUN echo "PHP-moduler (extensions): "
RUN php -m
RUN echo "PHP-inier: "
RUN php -i

# Endre php.ini-fil
#RUN sed -i 's/;extension=mbstring/extension=mbstring/' /etc/php/7.2/cli/php.ini
#RUN sed -i 's/;extension=pdo_sqlite/extension=pdo_sqlite/' /etc/php/7.2/cli/php.ini
#RUN sed -i 's/;extension=gd2/extension=gd2/' /etc/php/7.2/cli/php.ini
#RUN sed -i 's/;extension=curl/extension=curl/' /etc/php/7.2/cli/php.ini
#RUN sed -i 's/;extension=xml/extension=xml/' /etc/php/7.2/cli/php.ini
RUN sed -i 's/display_errors = Off/display_errors = On/' /etc/php/7.2/cli/php.ini
RUN sed -i 's/display_startup_errors = Off/display_startup_errors = On/' /etc/php/7.2/cli/php.ini

# Kopierer inn alt for å kjøre
COPY . .
RUN npm run setup

CMD npm start
