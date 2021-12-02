#!/bin/bash

#
# PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
# @version      phpMongoAdminInstall.sh 1001 26/11/21, 6:44 pm  Gilbert Rehling $
# @package      PhpMongoAdmin\setup\remote-scripts\
# @subpackage   docker-all.sh
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
#  Run to install on Linux:
#  wget https://phpmongoadmin.com/installation/custom.sh -O - | bash
#

COLOR_NONE="$(tput sgr0)"
COLOR_RED="$(tput setaf 1)"
COLOR_BLUE="$(tput setaf 4)"
COLOR_BLACK="$(tput setaf 0)"
COLOR_GREEN="$(tput setaf 6)"
COLOR_YBG="$(tput setab 11)"
COLOR_WBG="$(tput setab 15)"
COLOR_BBG="$(tput setab 12)"

# check is sudo
if [[ $UID != 0 ]]; then
	echo "${COLOR_RED}You must be 'sudo' to run this installation script"
	exit 1
fi

# check git is available
if ! command -v git &> /dev/null
then
	echo "${COLOR_RED}You must have 'git' installed to clone the repository"
	exit 1
fi

# check docker-compose is available
if ! command -v docker-composer &> /dev/null
then
	echo "${COLOR_RED}You must have 'docker & docker-compose' installed to use this installation"
	exit 1
fi

# confirm
WDIR=$( cd "$( dirname )" && pwd );
echo "${COLOR_GREEN}Setup location: $WDIR"

# clone
# ToDo: remember to update to 'master'
git clone --branch master https://github.com/php-mongo/docker-compose-app .

# list files
ls -la

# Notify
echo "${COLOR_RED}${COLOR_WBG}"
echo "${COLOR_BLUE}${COLOR_YBG}---------------------------------------- "
echo "${COLOR_BLUE}${COLOR_YBG}Stage 1 complete, application cloned to: $WDIR"
echo "${COLOR_BLUE}${COLOR_YBG}---------------------------------------- "
echo "${COLOR_RED}${COLOR_WBG}"
echo "${COLOR_RED}${COLOR_WBG}Docker Compose App: installs Apache2 and PhpMongoAdmin only."
echo "${COLOR_RED}${COLOR_WBG}PhpMongoAdmin's location within docker host container: /usr/share/phpMongoAdmin"
echo
echo "${COLOR_RED}${COLOR_WBG}This custom installation provides an option to manually copy and prepare the .env file."
echo "${COLOR_RED}${COLOR_WBG}type: cp .env.example .env && nano .env"
echo
echo "${COLOR_RED}${COLOR_WBG}Please Note: the APP_KEY value will be auto generated during the installation."
echo "${COLOR_RED}${COLOR_WBG}Update and save .env file"
echo "${COLOR_RED}${COLOR_WBG}Alternatively, choose 'No' when prompted and provide your .env settings."
echo
echo "${COLOR_RED}${COLOR_WBG}Then:"
echo "${COLOR_RED}${COLOR_WBG}Initialise the setup script (required):"
echo "${COLOR_RED}${COLOR_WBG}type: source docker/pmasetup.sh"
echo
echo "${COLOR_RED}If you intend to generate a self-signed SSL certificate on AWS please consult this documentation:"
echo "${COLOR_RED}https://docs.aws.amazon.com/cloudhsm/latest/userguide/openssl-library-install.html"
echo "${COLOR_RED}This script will 'not' use the 'cloudhsm' library due to over complexity of setup requirements."
echo "${COLOR_BLUE}"
echo
echo "${COLOR_BLUE}To complete the installation run the setup command, copy/paste/enter to proceed:"
echo
echo "${COLOR_BLUE}Default docker-compose (all) install:"
echo "${COLOR_BLUE}type: pmasetup run"
echo
echo "${COLOR_BLUE}During the setup process:"
echo "${COLOR_BLUE}If you choose 'production' as the environment, when the 'php artisan migrate' command is triggerred you will be asked 'Do you really wish to run this command? (yes/no)' - you must enter yes so the first migration can complete"
echo
