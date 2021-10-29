/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      config.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   config.js
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
*   Define the API route we will be using
*/
let api_url = '',
    web_url = '';

/*
*   Set the API route during the build process
*/
switch ( process.env.NODE_ENV )  {
    case 'development':
    case 'dev':
    case 'local':
        api_url = './api/v1';
        web_url = './';
        break;

    case 'demo':
        api_url = '//demo.phpmongoadmin.com/api/v1';
        web_url = '//demo.phpmongoadmin.com';
        break;

    case 'testing':
        api_url = '//testing.phpmongoadmin.com/api/v1';
        web_url = '//testing.phpmongoadmin.com';
        break;

    case 'staging':
        api_url = './api/v1';
        web_url = './';
        break;

    case 'production':
        api_url = './api/v1';
        web_url = './';
        break;
}

export const MONGO_CONFIG = {
    API_URL: api_url,
    WEB_URL: web_url,
    SITE_NAME: 'PhpMongoAdmin',
    SITE_FULLNAME: 'PHP Mongo Admin',
    LANGUAGES: { en: 'English', zh: 'Chinese' }
};
