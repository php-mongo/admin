<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      errors.php 1001 6/8/21, 8:45 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   errors.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2021. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
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
| Error language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used for error and warning display.
|
*/

return [

    "idRequired" => "Unable to continue: the required ID has not been set correctly",
    "noDatabaseRole" => "Your account does not have any database roles",
    "noFieldUpdated" => "Nothing to save: no fields where updated",
    "processing" => "An error occurred processing the last request",

    "collection" => [
        "clearError" => "An error occurred clearing documents from %s collection!",
        "createError" => "An error has occurred creating the collection %s",
        "dropError" => "Error: the collection %s was not dropped!",
        "duplicateError" => "An error occurred duplicating this collection",
        "error" => "Please add a name for your collection",
        "explainError" => "An error occurred fetching this query results",
        "indexError" => "Index was not created",
        "logsActionError" => "An error occurred fetching the query logs",
        "msgQueryError" => "The query process encountered errors - try adding &quot;'s around your elements",
        "propertiesError" => "This collection properties could not be updated",
        "renameError" => "An error occurred while renaming this collection",
        "unableToReadCollection" => "Unable to fetch collection: loading from Database dataset",
        "validateError" => "An error occurred attempting to validate this collection",
    ],

    "document" => [
        "createError" => "An error occurred while inserting the document",
        "deleteFailed" => "Document could no be deleted",
        "duplicateError" => "Document duplication failed with id %s errors",
        "errors" => "There was %s error encountered updating documents",
        "errorsPlural" => "There were %s errors encountered updating documents",
        "errorsCreate" => "There was %s error encountered inserting new documents",
        "errorsCreatePlural" => "There were %s errors encountered inserting new documents",
        "errorsDuplicate" => "There was %s error encountered duplicating documents",
        "errorsDuplicatePlural" => "There were %s errors encountered duplicating documents",
        "noDocuments" => "There are no documents to update. A new document will be created automatically",
        "noChange" => "No change detected - please update the document before saving",
        "updateError" => "Document with id %s was not updated",
    ],

    "global" => [
        "passwordLength" => "Your password must be at least %s characters in length",
        "passwordMatch" => "Your passwords do not match",
        "passwordRequired" => "Please enter a password, min length ",
        "userRequired" => "Username is a required field",
    ],

    "servers" =>[
        "createError" => "Server configuration was not created, please try again",
        "deleteFailed" => "Server configuration was not deleted - please try again",
        "hostRequired" => "Please enter the host server name",
        "mongoCloudHost" => "You cannot use '%s' for a connection to mongo atlas cloud",
        "updateError" => "Server configuration was not updated, please try again",
    ],

    "users" => [
        "activateFailed" => "The user was not activated",
        "deactivateSuccess" => "The user was not deactivated",
        "deleteError" => "An error occurred deleting this user",
        "email" => "You must provide an email address for Application Login users",
        "incompatibleRoles" => "Incompatible roles selection detected: auto fix engaged",
        "name" => "Name is required",
        "password-content" => "Your password may not contain your username",
        "role" => "You must select at least one role",
        "type" => "You must select a type of user account",
        "user-manage-permission" => "Your account does not have user management permissions",
    ]

];
