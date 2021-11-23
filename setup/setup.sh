#!/bin/bash

#
# PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
# @version      setup.sh 1001 23/11/21, 7:33 pm  Gilbert Rehling $
# @package      PhpMongoAdmin\setup
# @subpackage   setup.sh
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

PMA_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );

setup() {
  SOURCE="./env.example"
  TARGET="./.env"
  GLOBAL_FILE="phpMongoAdmin.conf"
  GLOBAL_SOURCE="./setup/apache/global/phpMongoAdmin.conf"
  #VIRTUAL_FILE="vhost_phpMongoAdmin"
  VIRTUAL_SOURCE="./setup/apache/virtualHost/vhost_phpMongoAdmin.conf"
  DATABASE="/usr/share/phpMongoAdmin/database/sqlite/database.sqlite"

  COLOR_RED="$(tput setaf 1)"
  COLOR_NONE="$(tput sgr0)"
  COLOR_BLUE="$(tput setaf 6)"

  echo "${COLOR_BLUE}Global apache.conf : $GLOBAL_SOURCE"
  echo "${COLOR_BLUE}Virtual apache.conf : $VIRTUAL_SOURCE"

  COMMAND=$1

  # Step 1: copy environment file
  copyEnvironment() {
    echo "${COLOR_BLUE}Env source : $SOURCE"
    echo "${COLOR_BLUE}Env target : $TARGET"
    cp $SOURCE $TARGET
  }

  # Step 2: setup database
  createDatabase() {
    echo "${COLOR_BLUE}Sqlite database path : $DATABASE"
    touch $DATABASE
  }

  # Step 3: run composer install
  composerInstall() {
    echo "${COLOR_BLUE}Running: composer install"
    composer install
  }

  # Step 4: run migrations
  databaseMigrations() {
    echo "${COLOR_BLUE}Running migrations: php artisan migrate"
    php artisan migrate
  }

  # Step 5: install Passport
  installPassport() {
    echo "${COLOR_BLUE}Installing passport: php artisan passport:install"
    php artisan passport:install
  }

  # Step 6: copy web config based on server found
  # Limited to /etc/apache2 & /etc/httpd type of installations
  copyApacheConfig() {
    echo "${COLOR_BLUE}Copying web config:"
    if [ -e /etc/apache2 ]; then
      echo "${COLOR_BLUE}Found /etc/apache2/~"
      cp $GLOBAL_SOURCE /etc/apache2/conf-available/$GLOBAL_FILE
      ln -s /etc/apache2/conf-available/$GLOBAL_FILE  /etc/apache2/conf-enabled/$GLOBAL_FILE
      systemctl restart apache2
      FOUND='apache2'
    fi;

    if [ -e /etc/httpd ]; then
      echo "${COLOR_BLUE}Found /etc/httpd/~"
      cp $GLOBAL_SOURCE /etc/httpd/conf.d/$GLOBAL_FILE
      systemctl restart httpd
      FOUND='httpd'
    fi;

    if [ ! $FOUND ]; then
      echo "${COLOR_BLUE}Error: unable to find apache2 or httpd to complete the web setup"
    fi
  }

  # Step 7: start job worker
  startQueue() {
    if [ $FOUND ]; then
      echo "${COLOR_BLUE}Application ready for loading"
    fi;

    echo "${COLOR_BLUE}Starting queue worker: php artisan queue:work"
    php artisan queue:work
  }

  # Call this command to run all sequenced commands in order
  setup() {
    echo "${COLOR_BLUE}Working DIR : $PMA_DIR"
    copyEnvironment
    createDatabase
    composerInstall
    databaseMigrations
    installPassport
    copyApacheConfig
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
