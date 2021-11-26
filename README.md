
## About PhpMongoAdmin

PhpMongoAdmin is a Web-based MongoDb management console, written in PHP and leveraging great tools like Laravel and Vue.
The familiar interface allows you to manage many different aspects of your MongoDB installation:

- MongoDB installation status and overview.
- Databases overview.
- User(s) authentication and authorization.
- Collections and objects management.
- Importing and exporting data.
- Processing overview.
- Administration tools.

PhpMongoAdmin is accessible, easy to setup, easy to learn and provides plenty of tools required for day to day MongoDB management.

## Learning MongoDB

PhpMongoAdmin provides full [documentation](https://phpmongoadmin.com/support/documentation) along with some video tutorials, making it a breeze to get started with the management interface.

If you've used other tools like PhpMyAdmin or the old RockMongo application then you'll find that using PhpMongoAdmin will be very familiar.

## Quick Install
This application currently supports a quick-install script for Linux distributions with Apache: tested on Centos & Ubuntu.  
The default installation location is: /usr/share/phpMongoAdmin  
We recommend using a website that supports SSL to access the application: https://your-domain.com/phpmongoadmin   
Execute the following command as a sudo user:
- wget https://phpmongoadmin.com/installation/phpMongoAdminInstall.sh -O - | bash
- You will get prompts for the following information and choices:
  - Will the installation be available for public access?
  - Select an environment: ( production, staging, local are valid choices )
  - Turn debugging mode on|off ( off recommended for all environments except local )
  - The URL you will access the application with ( this is not strictly enforced - its mostly used for reference )
- Once the process completes you can load the application and set-up the Control-User ( you will need to MongoDB credentials )
- !! Never expose a MongoDB to the public without activating secure access !!
  - https://docs.mongodb.com/guides/server/auth/

## PhpMongoAdmin Sponsors

We are very new, and have big plans for our up and coming range of MongoDB tools. If your keen to get involved as a Sponsor or Developer please let us know, we welcome all potential partners.

### Premium Partners

- **[MFMAW](https://mfmaw.com/)**

### Community Sponsors

- [LOTS OF ROOM HERE]
- [MFMAW](https://mfmaw.com)

## Contributing

Thank you for considering contributing to the PHP MongoDB Admin! The contribution guide can be found in the [PhpMongoAdmin documentation](https://phpmongoadmin.com/support/documentation/contributions).

## Code of Conduct

In order to ensure that our PHP MongoDB Tools community is welcoming to all, please review and abide by the [Code of Conduct](https://phpmongoadmin.com/support/documentation/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability in any PHP MongoDB Tools, please send an e-mail to Gilbert Rehling via [gilbert@phpmongoadmin.com](mailto:gilbert@phpmongoadmin.com). All security vulnerabilities will be promptly addressed.

## License

The PhpMongoAdmin application is open-sourced software licensed under the [GNU GPLv3 license](https://www.gnu.org/licenses/gpl-3.0.html).
