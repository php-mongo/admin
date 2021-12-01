#!/bin/bash

#
# PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
# @version      phpMongoAdminInstall.sh 1001 26/11/21, 6:44 pm  Gilbert Rehling $
# @package      PhpMongoAdmin\setup\remote-scripts\
# @subpackage   default.sh
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
#  wget https://phpmongoadmin.com/installation/default.sh -O - | bash
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

# go temp
cd /usr/share

# create working dir
mkdir phpMongoAdmin && cd phpMongoAdmin

# confirm
WDIR=$( cd "$( dirname )" && pwd );
echo "${COLOR_GREEN}Setup location: $WDIR"

# clone
# ToDo: remember to update to 'master'
git clone --branch testing https://github.com/php-mongo/admin .

# list files
ls -la

# Notify and present options
echo "${COLOR_BLUE}${COLOR_YBG "
echo "${COLOR_BLUE}${COLOR_YBG}--------------------------------------- "
echo "${COLOR_BLUE}${COLOR_YBG}Stage 1 complete, application cloned to: $WDIR"
echo "${COLOR_BLUE}${COLOR_YBG}--------------------------------------- "
echo "${COLOR_BLUE}${COLOR_WBG} "
echo "${COLOR_BLUE}Change directory to: $WDIR"
echo "${COLOR_BLUE}type: cd $WDIR"
echo "${COLOR_BLUE}Enter.."
echo
echo "${COLOR_BLUE}Initialise the setup script (required):"
echo "${COLOR_BLUE}type: source setup/pmainstall.sh"
echo
echo "${COLOR_BLUE}To complete the installation type a choose command option then copy/paste/enter to proceed:"
echo
echo "${COLOR_BLUE}Default (global) private install:"
echo "${COLOR_BLUE}type: pmainstall run default"
echo
echo "${COLOR_BLUE}Default (global) public install:"
echo "${COLOR_BLUE}type: pmainstall run default public"
echo
echo "${COLOR_BLUE}VirtualHost private install:"
echo "${COLOR_BLUE}type: pmainstall run vhost"
echo
echo "${COLOR_BLUE}VirtualHost public install:"
echo "${COLOR_BLUE}type: pmainstall run vhost public"
echo
echo "${COLOR_BLUE}During the setup process:"
echo "${COLOR_BLUE}If you choose 'production' as the environment, when the 'php artisan migrate' command is triggerred you will be asked 'Do you really wish to run this command? (yes/no)' - you must enter yes so the first migration can complete"
echo
