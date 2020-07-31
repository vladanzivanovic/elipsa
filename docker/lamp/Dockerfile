FROM ubuntu:latest

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update
RUN apt-get install -y apt-utils
RUN apt-get install -y vim
RUN apt-get install -y curl
RUN apt-get install -y apache2
RUN a2enmod rewrite

RUN apt-get install -y software-properties-common
RUN add-apt-repository ppa:ondrej/php

RUN apt-get update -y
RUN apt-get upgrade -y

RUN apt-get install -y php7.3
RUN apt-get install -y libapache2-mod-php7.3
RUN apt-get install -y php7.3-mysql
RUN apt-get update -y
RUN apt-get install -y php7.3-gd 
RUN apt-get install -y php7.3-imagick 
RUN apt-get install -y php7.3-recode 
RUN apt-get install -y php7.3-tidy 
RUN apt-get install -y php7.3-xmlrpc
RUN apt-get install -y php7.3-xml
RUN apt-get install -y php7.3-cli
RUN apt-get install -y php7.3-common
RUN apt-get install -y php7.3-curl
RUN apt-get install -y php7.3-mbstring
RUN apt-get install -y php7.3-zip
RUN apt-get install -y php-intl

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -

RUN apt-get update
RUN apt-get install -y nodejs
RUN apt-get install -y net-tools
RUN apt-get install -y git

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
# Make sure we're installing what we think we're installing!
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
&& rm -f /tmp/composer-setup.*

RUN a2enmod headers

COPY 000-default.conf /etc/apache2/sites-available

RUN phpenmod intl

WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
