<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      collection.php 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   collection.php
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
| Import component Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used within the database import panel.
|
*/
return [

    "actionDefault" => "Please ensure the correct file type is selected.",
    "actionSuccess" => "Import was successful",
    "compressed" => "GZIPED (compressed) : is this file a *.gz file?",
    "current" => "The current database (%s) will be used for all imported collections.",
    "default" => "The collection (%s) will be used for JSON imports and for *.js imports where the import target collection is deselected.",
    "file" => "Choose File",
    "fileAdmin" => "Choose a *.js file exported from PhpMongoAdmin or JS with seperated inserts",
    "fileMongo" => "Choose a *.json file exported from MongoDB Shell",
    "jsonContent" => "JSON content will be added to an existing collection",
    "import" => "Import",
    "indexes" => "Indexes",
    "replace" => "Replace any existing (duplicate) collections",
    "title" => "Database Import",
    "useImportCollection" => "Use collection defined in Import? Uncheck to use default (1st) collection",
    "useSelectedCollection" => "Use the pre-selected target collection or select another"

];
