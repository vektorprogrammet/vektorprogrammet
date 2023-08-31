FROM debian:buster-slim

SHELL ["/bin/bash", "-c"] 

# nvm environment variables
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 14.21.3

RUN apt-get update \
    && apt-get -y install curl git gnupg unzip wget \
        lsb-release apt-transport-https ca-certificates \
    #
    # Set up php-repo for apt
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list \
    #
    # Install php
    && apt-get update \
    && apt-get -y install php7.4 php7.4-mbstring php7.4-sqlite \
                php7.4-gd php7.4-curl php7.4-xml php7.4-zip \
    #
    # Install NodeJS
    && mkdir $NVM_DIR \
    # https://github.com/creationix/nvm#install-script
    && curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v0.39.5/install.sh | bash \
    && source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

WORKDIR /app

EXPOSE 8000

CMD ["npm", "run", "start"]
