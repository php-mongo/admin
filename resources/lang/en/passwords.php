<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      passwords.php 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   passwords.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    "passwordTitle" => "Password Resets",
    "passwordResetBold" => "At this time PhpMongoAdmin does not support user password resets",
    "passwordResetInfo" => "PhpMongoAdmin currently only supports password resets for the original control user.<br>All other users should make a request via a system administrator.<br>System admins can send password request token to users within their access scope.",
    "reset" => "Your password has been reset!",
    //"sent" => "We have emailed your password reset link!",
    "sent" => "Your password reset request was successful.<br>We have emailed your password reset link!<br>Please check your Inbox.<br>If you cannot find the email please check your junk-mail and or spam folders.",
    "throttled" => "Please wait before retrying.",
    "token" => "This password reset token is invalid.",
    "user" => "We can't find a user with that email address.",

];
