<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      global.php 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   global.php
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

$protocol = $_SERVER['REQUEST_SCHEME'] ?? 'http';

/*
|--------------------------------------------------------------------------
| Global Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used globally throughout the application.
|
*/

return [

    "cancel" => "Cancel",
    "claimAd" => "Claim This Ad",
    "claimOk" => "Ad claimed successfully",
    "clear" => "Clear",
    "clearList" => "Clear List",
    "close" => "Close",
    "copyright" => "Copyright &copy; 2020 PHP Mongo Admin. All Rights Reserved",
    "country" => "Country",
    "definition" => "Definition: ",
    "delete" => "Delete",
    "edit" => "Edit",
    "galleryView" => "Gallery",
    "less" => "Less",
    "loadingApplication" => "Loading application data: please wait...",
    "loading" => "loading: please wait...",
    "language" => "Language",
    "listView" => "List",
    "logo_" => "Logo",
    "logo" => "PHP Mongo Admin",
    "more" => "More",
    "no" => "No",
    "noRoles" => "Your MongoDb account does not not have any associated roles, or, you don't have a MongoDb account.<br>
    Please contact your system administrator for assistance.<br><br>
    If you have run the installation/setup process and landed here without a working configuration, you might need to run the setup process again.<br>
    Check that the application database was created during setup: site-root-directory/database/sqlite/database.sqlite<br>
    If this file was not created then the server may not have write access - this is usually tested during the setup process.<br>
    If the file does exist and you have forgotten your Control-User account details you can recover the account by running the setup script again.<br>
    <a href=\"" . $protocol . "://" . $_SERVER['SERVER_NAME'] . "/setup\">" . $_SERVER['SERVER_NAME'] . "/setup</a><br>
    The setup process will analise the application status and provide you with suitable options for recovery.<br><br>
    Alternatively, you may have to delete the database file that was created and run the setup process again.<br>
    Your MongoDb data will not be affected, however, you will need the mongodb credentials that you may have either been provided with or created during the database installation and setup.<br>
    Detailed installation and setup help is available via the PhpMongoAdmin support channel: <a href=\"https://phpmongoadmin.com/support\">phpmongoadmin.com/support</a>",
    "pleaseWait" => "Please Wait",
    "region" => "Region",
    "reset" => "Reset",
    "resetTitle" => "Reset Your Password",
    "resetEmailPlaceholder" => "Enter your email address here",
    "resetInfo" => "Enter your Email Address to reset your password",
    "resetSent" => "Password reset link sent successfully. Please check your Email and click the Password Reset Link or copy / paste the link into your browser",
    "resetSuccess" => "Your password has been reset successfully - please login to continue",
    "resetEmailInvalid" => "Invalid email address entered",
    "resetAccountError" => "Thank you. Email sent...",
    "resetSendError" => "Unknown error. Unable to send email at this moment",
    "resetExpired" => "Your password reset key has expired - please submit the request again",
    "selectLanguage" => "Select a Language for this Ad",
    "save" => "Save",
    "send" => "Send",
    "sendRequest" => "Send request",
    "search" => "Search",
    "searchPlaceholder" => "Search...",
    "siteTitle" => "PHP Mongo Admin",
    "state" => "State",
    "suburb" => "Suburb",
    "unknownError" => "An Unknown Error Has Occurred",
    "update" => "Update",
    "warning" => "Warning",
    "yes" => "Yes",

];
