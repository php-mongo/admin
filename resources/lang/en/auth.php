<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      auth.php 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   auth.php
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

/*
|--------------------------------------------------------------------------
| Authentication Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used during authentication for various
| messages that we need to display to the user. You are free to modify
| these language lines according to your application's requirements.
|
*/

return [

    "Bad-Request" => "Your request cannot be processed",
    "Unauthorized" => "Invalid login attempt. Please try again",
    "failed" => "These credentials do not match our records.",
    "Inactive" => "This account is inactive.\r\nPlease contact your application administrator for assistance",
    "invalidTokenTitle" => "Invalid Password Reset Token",
    "invalidTokenDetail" => "The token provides has expired or it is invalid.<br>Please select an option below to continue.",
    "throttle" => "Too many login attempts. Please try again in :seconds seconds.",
    "login-success" => "You have successfully logged in - please wait for redirection to PhpMongoAdmin",
    "logout-success" => "You have successfully logged out - please wait to be redirected",
    "resetFormInfo" => "Please enter your new password and password confirmation. Click the Reset Password button to submit the form.",
    "resetInfo" => "To enhance the security of this application, only the original Control-User may reset their password.<br> All other users must make a password reset request.",
    "resetTitle" => "Password Reset",
    "resetToken" => "Control User: <a href=\"setup\">return to setup page</a>",
    "requestToken" => "All other users: <a href=\"password/reset\">request reset token</a>",
    "unknown" => "The last action failed due to an unhandled error",
    "verifyEmailTitle" => "Please verify you email address",
    "verifyInfo" => "Only the control user must verify their email address",
    "verifyTitle" => "Please click below to continue",

];
