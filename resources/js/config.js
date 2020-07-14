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
