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
| Collection component Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used within the Collection panel.
|
*/

return [

    "action" => "Action",
    "addIndex" => "Create standard index",
    "add2dIndex" => "Create 2D index",
    "all" => "All",
    "asc" => "ASC",
    "array" => "Array",
    "bitPrecision" => "Bit precision",
    "buttonClear" => "Clear criteria",
    "buttonExplain" => "Explain",
    "buttonSubmit" => "Submit query",
    "bytes" => "bytes",
    "cancel" => "Cancel",
    "capped" => "Capped collection",
    "cappedCheck" => "Is capped?",
    "cappedOptions" => "Capped collection options",
    "cappedHelp" => "A Capped collection must have a size limit specified",
    "clear" => "Clear",
    "clearConfirm" => "Clear all documents from %s collection? This action cannot be reversed!",
    "clearSuccess" => "All documents from %s collection have been successfully cleared",
    "close" => "Close",
    "create" => "Create New Collection",
    "createIndex" => "Create Index",
    "createSuccess" => "Collection %s created successfully",
    "createButton" => "Create Collection",
    "collection" => "Collection",
    "collections" => "Collections",
    "collectionDuplicate" => "Duplicate Collection",
    "collectionExport" => "Export Collection(s)",
    "collectionImport" => "Import Collection(s)",
    "collectionIndexes" => "New Collection Index(s)",
    "collectionHistory" => "View Collection Query History",
    "collectionProperties" => "Update Collection Properties",
    "collectionRename" => "Rename Collection",
    "collectionStatistics" => "View Collection Statistics",
    "collectionValidation" => "Validate Active Collection",
    "compress" => "GZIP (compress)",
    "compressed" => "GZIPED (compressed) : is this file a *.gz file?",
    "cost" => "Cost",
    "count" => "Max document count",
    "dataSize" => "Data size",
    "dbAdminMessage" => "The dbAdmin and dbAdminAnyDatabase roles do not allow loading objects (documents) from non-system collections.<br>Please use the collection navigation tabs to see information and statistics for this collection.",
    "default" => "Default:",
    "delete" => "Delete",
    "desc" => "DESC",
    "displaying" => "Displaying",
    "documentCount" => "Document count",
    "documentMax" => "Max document count",
    "documents" => "documents",
    "download" => "Download?",
    "drop" => "Drop",
    "dropConfirm" => "Drop ( %s ) collection and all documents? This cannot be reversed!",
    "dropSuccess" => "Dropping %s collection was successfully completed",
    "duplicate" => "Duplicate",
    "duplicateName" => "Copy to name",
    "duplicateIndexes" => "Copy existing indexes",
    "duplicateSuccess" => "The collection was successfully copied to %s",
    "duplicating" => "Duplicating collection",
    "empty" => "The collection >> %s << has no documents",
    "expand" => "Expand",
    "explainInfo" => "View query explanation results below",
    "explainQuery" => "Explain Current Query",
    "explainSuccess" => "Query explain request was successful",
    "export" => "Export",
    "exportJson" => "Export as JSON - currently only available for single collections export",
    "fields" => "Fields",
    "file" => "File",
    "fileAdmin" => "Choose a *.js file exported from PhpMongoAdmin",
    "fileMongo" => "Choose a *.json file exported from MongoDB Shell",
    "findAll" => "findAll",
    "found" => "Found",
    "hints" => "Hints",
    "history" => "History",
    "import" => "Import",
    "indexes" => "Indexes",
    "indexCreationString" => "Index creation string",
    "indexDetails" => "Index details",
    "indexMetadata" => "Index metadata",
    "indexMetaFormat" => "Format version",
    "indexMetaInfo" => "Info object",
    "indexType" => "Index type",
    "indexesNumber" => "Number of indexes",
    "indexSizes" => "Index sizes",
    "indexSize" => "Index size",
    "indexSuccess" => "Index with name %s created successfully",
    "indexUri" => "Index URI",
    "insert" => "Insert",
    "json" => "JSON",
    "key" => "Key",
    "limit" => "Limit",
    "locationField" => "Location field",
    "logsAction" => "Found %s query logs - click 'Query again' to reuse",
    "logsActionEmpty" => "No query logs were found",
    "minBound" => "Min bound",
    "maxBound" => "Max bound",
    "modify" => "modify",
    "msgEmptyQuery" => "You query has no criteria - please add at least one (key:value | key=>value) pair",
    "msgEmptyResult" => "No results were returned - try changing your search criteria",
    "msgNoDocs" => "This collection has no documents to search or filter",
    "msgTooFew" => "All documents in this collection are already showing - try using the Filter",
    "name" => "Name",
    "namePlaceholder" => "MongoDB uses this optional value to create a name",
    "namespace" => "Namespace",
    "newField" => "New field",
    "newName" => "New name",
    "newObject" => "New object",
    "ok" => "OK",
    "operation" => "Operation",
    "options" => "Collection options",
    "otherFields" => "Other fields",
    "overwrite" => "Overwrite existing?",
    "placeholderCount" => "Optional - maximum number of documents",
    "placeholderName" => "Enter a name for the collection",
    "placeholderSize" => "Optional - maximum storage space in bytes",
    "properties" => "Properties",
    "propertiesSuccess" => "This collection properties were updated successfully",
    "query" => "Query",
    "queryAgain" => "Query again",
    "queryExamples" => "Query examples",
    "refresh" => "Refresh",
    "remove" => "remove",
    "rename" => "Rename",
    "renameInfo" => "Enter a new name for this collection below",
    "renameSuccess" => "The collection was successfully renamed to %s",
    "rows" => "Rows",
    "save" => "Save",
    "size" => "Max data size",
    "sparse" => "Spares",
    "statistics" => "Statistics",
    "text" => "Text",
    "transfer" => "Transfer",
    "unique" => "Unique",
    "update" => "Update",
    "updating" => "Updating",
    "useCurrent" => "Use current collection (%s) : uncheck to use the collection defined in the exported file",
    "validate" => "Validate",
    "validateInfo" => "Review the collection validation results below",
    "validateSuccess" => "This collection was validated successfully",
    "version" => "Version"

];
