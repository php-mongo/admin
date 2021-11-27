#!/bin/bash

#
# PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
# @version      phpMongoAdminInstall.sh 1001 26/11/21, 6:44 pm  Gilbert Rehling $
# @package      PhpMongoAdmin\resources
# @subpackage   phpMongoAdminInstall.sh
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
#  wget https://phpmongoadmin.com/installation/phpMongoAdminInstall.sh -O - | bash
#

COLOR_RED="$(tput setaf 1)"
COLOR_NONE="$(tput sgr0)"
COLOR_BLUE="$(tput setaf 6)"

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
echo "${COLOR_BLUE}Setup location: $WDIR"

# clone
# ToDo: remember to update to 'master'
git clone --branch testing https://github.com/php-mongo/admin .

# list files
ls -la

# run setup
source setup/pmasetup.sh

# Notify
echo "${COLOR_BLUE}Stage 1 complete: application cloned to $WDIR"
echo
echo "${COLOR_BLUE}Switch to: $WDIR"
echo "${COLOR_BLUE}type: cd $WDIR"
echo
echo "${COLOR_BLUE}To complete the installation type a setup command option then hit enter to start:"
echo
echo "${COLOR_BLUE}Default (global) private install:"
echo "${COLOR_BLUE}type: pmasetup run default"
echo
echo "${COLOR_BLUE}Default (global) public install:"
echo "${COLOR_BLUE}type: pmasetup run default public"
echo
echo "${COLOR_BLUE}VirtualHost private install:"
echo "${COLOR_BLUE}type: pmasetup run vhost"
echo
echo "${COLOR_BLUE}VirtualHost public install:"
echo "${COLOR_BLUE}type: pmasetup run vhost public"

# get response
#read -e -p -r "${COLOR_BLUE}Will this be a publicly available installation (y/n)? " -i "y" answer
#if [[ $answer == "y" || $answer == "Y" ]]; then
#    pmasetup run public
#else
#    pmasetup run
#fi
