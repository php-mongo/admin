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

COLOR_RED="$(tput setaf 1)"
COLOR_NONE="$(tput sgr0)"
COLOR_BLUE="$(tput setaf 6)"

# check is sudo
if [ ! whoami | grep 'root' ]; then
	echo "${COLOR_RED}You must be 'sudo' to run this installation script"
	return 0
fi

# check git is available
if [ ! whereis git | grep 'git' ]; then
	echo "${COLOR_RED}You must have 'git' installed to clone the repository"
	return 0
fi

# go temp
cd /tmp

# create working dir
mkdir pmasetup && cd pmasetup

TMP_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd );
echo  echo "${COLOR_RED}PWD: $TMP_DIR"
