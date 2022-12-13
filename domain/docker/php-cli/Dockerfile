FROM debian:buster-slim

ENV DEBIAN_FRONTEND=noninteractive
ENV WORKSPACE_DIR=/opt/project
ENV TZ=America/Sao_Paulo
ENV COMPOSER_HOME=/composer
ENV USER_DOCKER=ixcsoft
ENV HOME_USER_DOCKER=/home/$USER_DOCKER

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        libzip-dev \
        unzip \
        zip \
    && apt-get clean \
    && apt-get autoclean \
    && apt-get autoremove --yes \
    && rm -rf /var/lib/{apt,dpkg,cache,log}/ \
    && rm -rf /tmp/*

RUN adduser --disabled-password --gecos '' $USER_DOCKER \
  && usermod -aG sudo,www-data $USER_DOCKER \
  && echo "$USER_DOCKER:x:1000:1000:$USER_DOCKER,,,:/home/$USER_DOCKER:/bin/bash" >> /etc/passwd \
  && echo "$USER_DOCKER:x:1000:" >> /etc/group \
  && echo "$USER_DOCKER ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers \
  && mkdir -p /opt/project \
  && chown $USER_DOCKER:$USER_DOCKER /opt/project \
  && rm -rf /var/lib/{apt,dpkg,cache,log}/ \
  && rm -rf /tmp/*

RUN cd /tmp \
  && curl -L -o sury.sh https://packages.sury.org/php/README.txt \
  && chmod +x sury.sh \
  && sh sury.sh \
  && apt-get install -y  \
    php8.1-apcu \
    php8.1-cli  \
    php8.1-curl \
    php8.1-dom \
    php8.1-mbstring \
    php8.1-memcached \
    php8.1-pdo \
    php8.1-pgsql \
    php8.1-redis \
    php8.1-yaml \
    php8.1-xdebug \
    php8.1-zip \
  && apt-get clean \
  && apt-get autoclean \
  && apt-get autoremove --yes \
  && rm -rf /var/lib/{apt,dpkg,cache,log}/ \
  && rm -rf /tmp/*

RUN touch /etc/php/8.1/mods-available/treinamento.ini \
  && echo 'display_errors=on' > /etc/php/8.1/mods-available/treinamento.ini \
  && echo 'display_startup_errors=on' > /etc/php/8.1/mods-available/treinamento.ini \
  && echo 'error_reporting=E_ALL' > /etc/php/8.1/mods-available/treinamento.ini \
  && phpenmod treinamento

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer