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

pmasetup() {
  SOURCE="./.env.example"
  TARGET="./.env"
  CONFIG_FILENAME="phpMongoAdmin.conf"
  GLOBAL_SOURCE="setup/apache/global/phpMongoAdmin.conf"
  GLOBAL_SOURCE_PUBLIC="setup/apache/global/phpMongoAdminPublic.conf"
  #VIRTUAL_FILE="vhost_phpMongoAdmin"
  VIRTUAL_SOURCE="setup/apache/virtualHost/vhost_phpMongoAdmin.conf"
  VIRTUAL_SOURCE_PUBLIC="setup/apache/virtualHost/vhost_phpMongoAdminPublic.conf"
  DATABASE="database/sqlite/database.sqlite"

  COLOR_RED="$(tput setaf 1)"
  COLOR_NONE="$(tput sgr0)"
  COLOR_BLUE="$(tput setaf 6)"

  PHP=/usr/bin/php7.4
  COMPOSER=/usr/bin/composer

  echo -n "${COLOR_BLUE}Global apache.conf : $GLOBAL_SOURCE"
  echo -n "${COLOR_BLUE}Virtual apache.conf : $VIRTUAL_SOURCE"

  COMMAND=$1
  CONTEXT=$2
  PUBLIC=$3

  # Step 1: copy and setup environment file
  copyEnvironment() {
    echo -n "${COLOR_BLUE}Env source : $SOURCE"
    echo -n "${COLOR_BLUE}Env target : $TARGET"
    cp "$PMA_DIR/$SOURCE" "$PMA_DIR/$TARGET"

    # set environment
    read -p "${COLOR_BLUE}Enter the preferred environment: " -i "local" env
    sed -i "s|APP_ENV=local|APP_ENV=$env|" .env

    # set debug mode
    if [ "$env" == "production" ]; then
      read -p "${COLOR_BLUE}Enable debug mode: false (highly recommended for production): " -i "false" debug
    elif [ "$env" == "local" ]; then
      read -p "${COLOR_BLUE}Enable debug mode: true (recommended for local with URL: localhost): " -i "true" debug
    else
      read -p "${COLOR_BLUE}Enable debug mode: false (recommended): " -i "false" debug
    fi;
    sed -i "s|APP_DEBUG=true|APP_DEBUG=$debug|" .env

    # set URL
    if [ "$env" == "production" ]; then
      read -p "${COLOR_BLUE}Enter the URL you will use to access the PhpMongoAdmin (https://myapp.com) : " url
    elif [ "$env" == "local" ]; then
      read -p "${COLOR_BLUE}Enter the APP URL: (http://localhost recommended for local environment): " -i "http://localhost" url
    else
      read -p "${COLOR_BLUE}Enter the APP URL: https://some-domain/.co: " url
    fi
    sed -i "s|http://localhost|$url|" .env
  }

  # Step 2: setup database
  createDatabase() {
    echo -n "${COLOR_BLUE}Sqlite database path : $PMA_DIR/$DATABASE"
    $(touch $PMA_DIR/$DATABASE)
    # update db path in .env
    sed -i "s|/usr/share/phpMongoAdmin/database/sqlite/database.sqlite|$PMA_DIR/$DATABASE|g" .env
  }

  # Step 3: run composer install
  composerInstall() {
    echo -n "${COLOR_BLUE}Running: composer install"
    # Issues with PHP 8 when requirements are based on php7.2+
    # ToDo: Make this use a dynamic search for correct PHP location
    $($PHP $COMPOSER install)
  }

  # Step 4: create application key
  generateKey() {
    $PHP artisan key:generate --ansi
  }

  # Step 5: run migrations
  databaseMigrations() {
    echo -n "${COLOR_BLUE}Running migrations: php artisan migrate"
    $PHP artisan migrate
  }

  # Step 6: install Passport
  installPassport() {
    echo "${COLOR_BLUE}Installing passport: php artisan passport:install"
    $PHP artisan passport:install
  }

  # Step 7: copy web config based on server found
  # Limited to /etc/apache2 & /etc/httpd based installations
  ##shellcheck#disable=SC2120
  copyApacheConfig() {
    # Set source based on provide context
    # ToDo: add test and config for vitualhost
    echo "${COLOR_BLUE}Context: $CONTEXT"
    if [ "$PUBLIC" == "public" ]; then
      echo "${COLOR_BLUE}Create public config:"
      GLOBAL_CONFIG="$PMA_DIR/$GLOBAL_SOURCE_PUBLIC"
    else
      echo "${COLOR_BLUE}Create local config:"
      GLOBAL_CONFIG="$PMA_DIR/$GLOBAL_SOURCE"
    fi;

    # In case its not a default location - update path inside the config
    echo "${COLOR_BLUE}Updating : $GLOBAL_CONFIG"
    sed -i "s|/usr/share/phpMongoAdmin/|$PMA_DIR/|g" "$GLOBAL_CONFIG"

    # Copy config to correct location
    echo "${COLOR_BLUE}Copying web config:"
    if [ -e /etc/apache2 ]; then
      echo "${COLOR_BLUE}Found /etc/apache2/~"
      cp "$GLOBAL_CONFIG" /etc/apache2/conf-available/$CONFIG_FILENAME
      ln -s /etc/apache2/conf-available/$CONFIG_FILENAME  /etc/apache2/conf-enabled/$CONFIG_FILENAME
      systemctl restart apache2
      FOUND="apache2"
    fi;

    if [ -e /etc/httpd ]; then
      echo "${COLOR_BLUE}Found /etc/httpd/~"
      cp "$GLOBAL_CONFIG" /etc/httpd/conf.d/$CONFIG_FILENAME
      systemctl restart httpd
      FOUND='httpd'
    fi;

    # Notify error
    if [ ! $FOUND ]; then
      echo "${COLOR_BLUE}Error: unable to find apache2 or httpd to complete the web setup"
    fi
  }

  # Step 8: set app file permissions
  setPermissions() {
    echo "${COLOR_BLUE}Setting application file ownership"
    chown -R www-data *
  }

  # Step 9: start job worker
  startQueue() {
    # Notify success
    if [ $FOUND ]; then
      echo "${COLOR_BLUE}Your application ready for loading in a browser"
    fi;

    echo "${COLOR_BLUE}Starting queue worker: php artisan queue:work"
    $PHP artisan queue:work
  }

  # Call this command to run all sequenced commands in order
  setup() {
    echo "${COLOR_BLUE}Working DIR : $PMA_DIR"
    copyEnvironment
    createDatabase
    composerInstall
    generateKey
    databaseMigrations
    installPassport
    copyApacheConfig
    setPermissions
    startQueue
  }

  # Handle request function
  case $COMMAND in
    run)
      setup
      ;;

    help)
      fmtHelp () {
        echo "-- ${COLOR_BLUE}$1${COLOR_NONE}: $2"
      }
      HELP="Available actions:
        $(fmtHelp "run" "Starts the installation process")"

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
