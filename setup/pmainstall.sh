#!/bin/bash

#
# PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
# @version      setup.sh 1001 23/11/21, 7:33 pm  Gilbert Rehling $
# @package      PhpMongoAdmin\setup
# @subpackage   pmasetup.sh
# @link         https://github.com/php-mongo/admin PHP MongoDB Admin
# @copyright    Copyright (c) 2021. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
# @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
# @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
#  php-mongo-admin - License conditions:
#  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
#  This web application is available as Free Software and has no implied warranty or guarantee of usability.
#  See licence.txt for the complete licensing outline.
#  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
#  See COPYRIGHT.php for copyright notices and further details.
#
# To prompt for DB password in Docker:
# read -s -p "Enter a Password for MongoDB root user: " x
#

# Need to run 1 level up from the scripts location
PMA_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd );

pmainstall() {
  SOURCE="./.env.example"
  TARGET="./.env"
  CONFIG_FILENAME="phpMongoAdmin.conf"
  NGINX_FILENAME="server_phpMongoAdmin.conf"
  VHOST_FILENAME="vhost_phpMongoAdmin.conf"
  GLOBAL_SOURCE="setup/apache/global/phpMongoAdmin.conf"
  GLOBAL_SOURCE_PUBLIC="setup/apache/global/phpMongoAdminPublic.conf"
  GLOBAL_NGINX_SOURCE="setup/nginx/global/phpMongoAdmin.conf"
  #GLOBAL_NGINX_SOURCE_PUBLIC="setup/apache/global/phpMongoAdminPublic.conf"
  VIRTUAL_SOURCE="setup/apache/virtualHost/vhost_phpMongoAdmin.conf"
  VIRTUAL_SOURCE_PUBLIC="setup/apache/virtualHost/vhost_phpMongoAdminPublic.conf"
  VIRTUAL_NGINX_SOURCE="setup/nginx/serverBlock/server_phpMongoAdmin.conf"
  #VIRTUAL_NGINX_SOURCE_PUBLIC="setup/apache/virtualHost/vhost_phpMongoAdminPublic.conf"
  DATABASE="database/sqlite/database.sqlite"

  COLOR_NONE="$(tput sgr0)"
  COLOR_RED="$(tput setaf 1)"
  COLOR_BLUE="$(tput setaf 4)"
  COLOR_BLACK="$(tput setaf 0)"
  COLOR_GREEN="$(tput setaf 6)"
  COLOR_WHITE="$(tput setaf 15)"
  COLOR_YBG="$(tput setab 11)"
  COLOR_WBG="$(tput setab 15)"
  COLOR_BBG="$(tput setab 12)"

  # find PHP
  if [ -e /usr/bin/php7.4 ]; then
    PHP=/usr/bin/php7.4
    echo "${COLOR_BLUE}Found php7.4"
  elif [ -e /usr/bin/php7.3 ]; then
    PHP=/usr/bin/php7.3
    echo "${COLOR_BLUE}Found php7.3"
  elif [ -e /usr/bin/php7.2 ]; then
    PHP=/usr/bin/php7.2
    echo "${COLOR_BLUE}Found php7.2"
  elif [ -e /usr/bin/php ]; then
    PHP=/usr/bin/php
    echo "${COLOR_BLUE}Found php?"
  elif [ -e /usr/bin/php8.0 ]; then
    PHP=/usr/bin/php8.0
    echo "${COLOR_BLUE}Found php8.0"
  fi;

  # find composer
  if [ -e /usr/bin/composer ]; then
    COMPOSER=/usr/bin/composer
  fi;

  echo
  echo "${COLOR_BLUE}${COLOR_WBG}Global apache.conf : $GLOBAL_SOURCE"
  echo "${COLOR_BLUE}Virtual apache.conf : $VIRTUAL_SOURCE"

  COMMAND=$1
  CONTEXT=$2
  PUBLIC=$3

  # Step 1: copy and setup environment file
  copyEnvironment() {
    # check .env has been copied
    if [ -e .env ]; then
      echo "${COLOR_GREEN}Environment settings file : $TARGET"
    else
      echo "${COLOR_GREEN}Environment source : $SOURCE"
      echo "${COLOR_GREEN}Environment target : $TARGET"
      cp "$PMA_DIR/$SOURCE" "$PMA_DIR/$TARGET"
    fi;

    echo "${COLOR_GREEN}Have you pre-populated the .env file parameters?"
    select done in Yes No;
    do
      if [ "$done" == "No" ]; then
        # set environment
        read -e -p "${COLOR_GREEN}Enter an environment (production, staging, local): " -i "local" env
        sed -i "s|APP_ENV=local|APP_ENV=$env|" .env

        # set debug mode
        if [ "$env" == "production" ]; then
          read -e -n5 -p "${COLOR_GREEN}Enable debug mode: false (highly recommended for production): " -i "false" debug
        elif [ "$env" == "local" ]; then
          read -e -n5 -p "${COLOR_GREEN}Enable debug mode: true (recommended for local with URL: localhost): " -i "true" debug
        else
          read -e -n5 -p "${COLOR_GREEN}Enable debug mode: false (recommended): " -i "false" debug
        fi;
        sed -i "s|APP_DEBUG=true|APP_DEBUG=$debug|" .env

        # set URL
        if [ "$env" == "production" ]; then
          read -p "${COLOR_GREEN}Enter the URL you will use to access the PhpMongoAdmin (https://myapp.com) : " url
        elif [ "$env" == "local" ]; then
          read -e -p "${COLOR_GREEN}Enter the APP's URL: (http://localhost recommended for local environment): " -i "http://localhost" url
        else
          read -p "${COLOR_GREEN}Enter the APP's URL: https://some-domain/.co: " url
        fi
        sed -i "s|http://localhost|$url|" .env
      fi;
      break;
    done;
  }

  # Step 2: setup database
  createDatabase() {
    echo "${COLOR_GREEN}Sqlite database path : $PMA_DIR/$DATABASE"
    $(touch $PMA_DIR/$DATABASE)
    # update db path in .env
    sed -i "s|/usr/share/phpMongoAdmin/database/sqlite/database.sqlite|$PMA_DIR/$DATABASE|g" .env
  }

  # Step 3: run composer install
  composerInstall() {
    echo "${COLOR_GREEN}Running: composer install"

    if [ -z "$PHP" ]; then
      echo "${COLOR_RED}${COLOR_WBG}-----------------------------------"
      echo "${COLOR_RED}${COLOR_WBG}PHP: $PHP "
      echo "${COLOR_RED}${COLOR_WBG}PHP was not found!"
      echo "${COLOR_RED}${COLOR_WBG}Please check: https://www.php.net/manual/en/install.php"
      echo "${COLOR_RED}${COLOR_WBG}-----------------------------------"
      exit 1
    fi;

    if [ -z "$COMPOSER" ]; then
      echo "${COLOR_RED}${COLOR_WBG}-----------------------------------"
      echo "${COLOR_RED}${COLOR_WBG}COMPOSER: $COMPOSER "
      echo "${COLOR_RED}${COLOR_WBG}Composer was not found!"
      echo "${COLOR_RED}${COLOR_WBG}Please check: https://getcomposer.org/"
      echo "${COLOR_RED}${COLOR_WBG}-----------------------------------"
      exit 1
    fi;

    # Issues occurred when PHP 8 is installed for CLI and composer requirements are based on php7.*
    $($PHP $COMPOSER install)
  }

  # Step 4: create application key
  generateKey() {
    $PHP artisan key:generate --ansi
  }

  # Step 5: run migrations
  databaseMigrations() {
    echo "${COLOR_GREEN}Running migrations: php artisan migrate"
    $PHP artisan migrate
  }

  # Step 6: install Passport
  installPassport() {
    echo "${COLOR_GREEN}Installing passport: php artisan passport:install"
    $PHP artisan passport:install
  }

  # Step 7: copy web config based on server found
  # Limited to /etc/apache2, /etc/httpd and /etc/nginx based installations
  copyApacheConfig() {
    # Set source based on provide context
    if [ "$CONTEXT" == "default" ]; then
      echo "${COLOR_GREEN}Default (global) context: $CONTEXT"
      if [ "$PUBLIC" == "public" ]; then
        echo "${COLOR_GREEN}Sourcing public config:"
        if [ -e /etc/nginx ]; then
          # ToDo: create public and private Nginx versions
          GLOBAL_CONFIG="$PMA_DIR/$GLOBAL_NGINX_SOURCE"
        else
          GLOBAL_CONFIG="$PMA_DIR/$GLOBAL_SOURCE_PUBLIC"
        fi;
      else
        echo "${COLOR_GREEN}Create local config:"
        if [ -e /etc/nginx ]; then
          GLOBAL_CONFIG="$PMA_DIR/$GLOBAL_NGINX_SOURCE"
        else
          GLOBAL_CONFIG="$PMA_DIR/$GLOBAL_SOURCE"
        fi;
      fi;
    else
      echo "${COLOR_GREEN}Virtual Host context: $CONTEXT"
      if [ "$PUBLIC" == "public" ]; then
        echo "${COLOR_GREEN}Create public config:"
        if [ -e /etc/nginx ]; then
          GLOBAL_CONFIG="$PMA_DIR/$VIRTUAL_NGINX_SOURCE"
        else
          GLOBAL_CONFIG="$PMA_DIR/$VIRTUAL_SOURCE_PUBLIC"
        fi;
      else
        echo "${COLOR_GREEN}Create local config:"
        if [ -e /etc/nginx ]; then
          GLOBAL_CONFIG="$PMA_DIR/$VIRTUAL_NGINX_SOURCE"
        else
          GLOBAL_CONFIG="$PMA_DIR/$VIRTUAL_SOURCE"
        fi;
      fi;
    fi;

    # In case its not a default location - update path inside the config
    echo "${COLOR_GREEN}Updating : $GLOBAL_CONFIG"

    if [ "$CONTEXT" == "vhost" ]; then
      # replace default paths
      sed -i "s|/var/hosting/sites/phpmongoadmin/|$PMA_DIR/|g" "$GLOBAL_CONFIG"

      # set hostname
      read -p "Enter the host / domain name for this application (localhost, host.local, host.your-domain.com): " host
      sed -i "s|host.yourdomain.com|$host|g" "$GLOBAL_CONFIG"

      if [ "$PUBLIC" == "public" ]; then
        echo "${COLOR_BLUE}${COLOR_WBG}Setting up SSL:"
        echo "${COLOR_BLUE}${COLOR_WBG}Do you already have an SSL certificate for the intended host?"
        select answer in Yes No Cancel;
        do
          # if cancel no SSL will be available
          if [ "$answer" == "Cancel" ]; then
            break;
          fi;

          # prompt user for paths
          if [ "$answer" == "Yes" ]; then
            # get key path
            read -p "Please provide the full path to the server key file: " key
            sed -i "s|$PMA_DIR/storage/certs/fake-server.key|$key|g" "$GLOBAL_CONFIG"

            # get certificate path
            read -p "Please provide the full path to the ssl certificate file: " cert
            sed -i "s|$PMA_DIR/storage/certs/pma-fake-cert.crt|$cert|g" "$GLOBAL_CONFIG"
            break;
          fi;

          # begin self signed process
          CERTS="$PMA_DIR/storage/certs/"
          if [ "$answer" == "No" ]; then
            echo "${COLOR_BLUE}${COLOR_WBG}Beginning self-signed SSL generation for apache:"
            # this works on AWS
            # generate server key
            openssl genrsa 2048 > "$CERTS/pma-self-signed-key.key"
            # generate server CSR
            openssl req -new -key "$CERTS/pma-self-signed-key.key" -out "$CERTS/pma-self-signed-csr.csr"
            # generate CERT
            openssl x509 -req -days 365 -in "$CERTS/pma-self-signed-csr.csr" -signkey "$CERTS/pma-self-signed-key.key" -out "$CERTS/pma-self-signed-cert.crt"
            # update names
            if [ -e "$CERTS/pma-self-signed-key.key" ]; then
              sed -i "s|fake-server.key|pma-self-signed-key.key|g" "$GLOBAL_CONFIG"
            fi;

            if [ -e "$CERTS/pma-self-signed-cert.crt" ]; then
              sed -i "s|pma-fake-cert.crt|pma-self-signed-cert.crt|g" "$GLOBAL_CONFIG"
            fi;
            break;
          fi;

          # no SSl required??
          if [ "$answer" == "Cancel" ]; then
            echo "${COLOR_BLUE}${COLOR_WBG}SSL will not be set-up!"
            # comment out the SSl definitions
            sed -i "s|SSLCertificateKeyFile|#SSLCertificateKeyFile|g" "$GLOBAL_CONFIG"
            sed -i "s|SSLCertificateFile|#SSLCertificateFile|g" "$GLOBAL_CONFIG"
            break;
          fi;

          break;
        done;
      fi;
    else
      sed -i "s|/usr/share/phpMongoAdmin/|$PMA_DIR/|g" "$GLOBAL_CONFIG"
    fi;

    # Copy config to correct location
    COUNT=0
    echo "${COLOR_GREEN}Copying web config:"
    if [ -e /etc/apache2 ]; then
      echo "${COLOR_GREEN}Found /etc/apache2/~"

      # default as Alias config
      if [ "$CONTEXT" == "default" ]; then
        cp "$GLOBAL_CONFIG" /etc/apache2/conf-available/$CONFIG_FILENAME
        ln -s /etc/apache2/conf-available/$CONFIG_FILENAME  /etc/apache2/conf-enabled/$CONFIG_FILENAME
      fi;

      # vhost site configuration
      if [ "$CONTEXT" == "vhost" ]; then
        cp "$GLOBAL_CONFIG" /etc/apache2/sites-available/$VHOST_FILENAME
        ln -s /etc/apache2/sites-available/$VHOST_FILENAME  /etc/apache2/sites-enabled/$VHOST_FILENAME
      fi;

      COUNT=$((COUNT+1))
      FOUND="apache2"
      APACHE2="Apache"
    fi;

    if [ -e /etc/httpd ]; then
      echo "${COLOR_GREEN}Found /etc/httpd/~"
      # default as Alias config
      if [ "$CONTEXT" == "default" ]; then
        cp "$GLOBAL_CONFIG" /etc/httpd/conf.d/$CONFIG_FILENAME
      fi;
      # vhost site configuration
      if [ "$CONTEXT" == "vhost" ]; then
        if [ -e /etc/httpd/sites-available ]; then
          cp "$GLOBAL_CONFIG" /etc/httpd/sites-available/$VHOST_FILENAME
          ln -s /etc/httpd/sites-available/$VHOST_FILENAME  /etc/httpd/sites-enabled/$VHOST_FILENAME
        else
          cp "$GLOBAL_CONFIG" /etc/httpd/conf.d/$VHOST_FILENAME
        fi;
      fi;

      COUNT=$((COUNT+1))
      FOUND="httpd"
      HTTPD="Httpd"
    fi;

    if [ -e /etc/nginx ]; then
      echo "${COLOR_GREEN}Found /etc/nginx/~"
      # default as Alias config
      cp "$GLOBAL_CONFIG" /etc/nginx/conf.d/$NGINX_FILENAME

      COUNT=$((COUNT+1))
      FOUND="nginx"
      NGINX="Nginx"
    fi;

    # Notify error
    if [ ! $FOUND ]; then
      echo "${COLOR_GREEN}Error: unable to find apache2, httpd or nginx to complete the web setup"
      exit 1
    fi;
  }

  # Step 8: set app file permissions
  setPermissions() {
    echo "${COLOR_GREEN}Setting application file ownership"
    # for nginx
    if [ -e /etc/nginx ]; then
      chown -R nginx ./*
    else
      chown -R www-data ./*
    fi;

    # If using Nginx and permission errors persists - try
    # Run these on the application root's parent directory:
    ## !! NEVER RUN THIS ON THE SERVERS ROOT DIRECTORY !! ##
    # find ./ -type f exec chmod 664 {} \;
    # find ./ -type d exec chmod 775 {} \;
    # chgrp -R nginx ./< parent directory >
    ## or if you dont need write access for a user
    # chown -R nginx:nginx ./< parent directory >
    #
    ## For Apache
    # chgrp -R www-data ./< parent directory >
    ## or
    # chown -R www-data:www-data ./< parent directory >
  }

  # Step 9: restart the correct server
  restartServer() {
    if [ "$COUNT" -gt 1 ]; then
      echo "${COLOR_RED}Found more than one server: please select which service to restart?"
      select restart in $APACHE2 $HTTPD $NGINX;
      do
        case $restart in
          Nginx)
            systemctl restart nginx
            ;;

          Httpd)
            systemctl restart httpd
            ;;

          Apache2)
            systemctl restart apache2
            ;;

          *)
            return 1
            ;;
        esac;
        break;
      done;
    else
      case $FOUND in
        nginx)
          echo "${COLOR_GREEN}Restarting nginx"
          systemctl restart nginx
          proc="$(ps -x | grep /usr/sbin/nginx | grep -v grep)"
          ;;

        httpd)
          echo "${COLOR_GREEN}Restarting httpd"
          systemctl restart httpd
          proc="$(ps -x | grep /usr/sbin/httpd | grep -v grep)"
          ;;

        apache2)
          echo "${COLOR_GREEN}Restarting apache2"
          apachectl restart
          proc="$(ps -x | grep /usr/sbin/apache2 | grep -v grep)"
          ;;

        *)
          return 1
          ;;
      esac;

      if [ -x "$proc" ]; then
          echo "${COLOR_RED}${COLOR_WHITE} Could not restart: $FOUND"
          echo "${COLOR_RED}${COLOR_WHITE}Try a manual restart:"
          echo "${COLOR_RED}${COLOR_WHITE}systemctl restart nginx:"
          echo "${COLOR_RED}${COLOR_WHITE}systemctl restart httpd"
          echo "${COLOR_RED}${COLOR_WHITE}systemctl restart apache2"
      fi;
    fi;
  }

  # Step 10: start job worker
  startQueue() {
    # Notify success
    if [ $FOUND ]; then
      echo "${COLOR_GREEN}Your application is ready for loading in a browser"
    fi;

    echo "${COLOR_GREEN}Starting queue worker: php artisan queue:work"
    $PHP artisan queue:work
  }

  # Call this command to run all sequenced commands in order
  setup() {
    echo "${COLOR_GREEN}Working DIR : $PMA_DIR"
    copyEnvironment
    createDatabase
    composerInstall
    generateKey
    databaseMigrations
    installPassport
    copyApacheConfig
    setPermissions
    restartServer
    startQueue
  }

  # Handle request function
  case $COMMAND in
    run)
      setup
      ;;

    help)
      fmtHelp () {
        echo "-- ${COLOR_GREEN}$1${COLOR_NONE}: $2"
      }
      HELP="Available actions:
        $(fmtHelp "run default" "Starts a default installation meant for private networks or localhost deployment")
        $(fmtHelp "run default public" "Starts th the default installation process for a public deployment")
        $(fmtHelp "run vhost" "Starts the vhost <VirtualHost> installation process for private or localhost deployment")
        $(fmtHelp "run vhost public" "Starts the vhost installation process for public deployment - includes SSL integration")"

      echo "${COLOR_NONE}$HELP"
      ;;

    *)
      echo "${COLOR_RED}Unknown action provided"
      echo

      setup help
      return 1
  esac

  return 0
}
