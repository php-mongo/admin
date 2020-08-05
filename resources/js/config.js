/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      config.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      config.js
 * @subpackage   Id
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See COPYRIGHT.php for copyright notices and further details.
 */

/*
*   Define the API route we will be using
*/
let api_url = '',
    web_url = '';

/*
*   Set the API route during the build process
*/

console.log(process.env.NODE_ENV);

switch ( process.env.NODE_ENV )  {
    case 'development':
    case 'dev':
    case 'local':
        api_url = '//pma.mongo.local/api/v1';
        web_url = '//pma.mongo.local';
        break;

    case 'staging':
        api_url = '//staging.phpmongoadmin.com/api/v1';
        web_url = '//staging.phpmongoadmin.com';
        break;

    case 'production':
        api_url = '//www.phpmongoadmin.com/api/v1';
        web_url = '//www.phpmongoadmin.com';
        break;
}

export const MONGO_CONFIG = {
    API_URL: api_url,
    WEB_URL: web_url,
    SITE_NAME: 'phpMongoAdmin',
    SITE_FULLNAME: 'PHP Mongo Admin',
    LANGUAGES: { en: 'English', zh: 'Chinese' }
};
