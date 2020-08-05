/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      DbsTextFilter.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      DbsTextFilter.js
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

export const DbsTextFilter = {
  methods: {
    processDbsTextFilter( db, text ) {
      /*
      *  Only process if the text is greater than 1
      */
      if ( text.length > 1 ) {
        /*
        *  If the db name matches the entered text return true otherwise return false.
        */
        if( db.name.toLowerCase().match( '[^,]*' + text.toLowerCase() + '[,$]*' )) {
            // db is matched
          return true;

        } else {
            // db is not matched
          return false;
        }

      } else {
          // error!! search text not founds
        return true;
      }
    }
  }
};
