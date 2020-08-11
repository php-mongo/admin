/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      convObj.js 1001 9/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   convObj.js
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
*   Import Vue
*/
import Vue from 'vue';

Vue.mixin( {
    beforeCreate() {
        const options = this.$options;
        if ( options.convObj )
            this.$convObj = options.convObj;

        else if ( options.parent && options.parent.$convObj )
            this.$convObj = options.parent.$convObj;
    }
});

/**
 * Class handling functions to convert Object to JSON, ARRAY, KEY/VALUE and visual versions for TEXTAREA editing
 */
export default function makeConvObj() {
    /**
     * Requires JS object
     * @pram    Object  obj
     */
    return function convObj(obj) {
         let type = typeof obj;
         let str  = false;
         if (type === 'object' && obj) {
             str = JSON.stringify( obj );
             str = str.replace("\"_id\":null,", "");

             console.log("convObj str: " + str);

         }

         return {
             json: () => {
                return str;
             },

             jsonV: (string) => {
              //   console.log("string: " + string);
                let input = string ? string : str;

                console.log("jsonv input: " + input);

                str            = input.replace(/{/g, "{\n\t");
                str            = str.replace(/:{/g, " : {\n\t");
                str            = str.replace(/,"/g, ",\n\t\"");
                str            = str.replace(/}/g, "\n}");
                str            = str.replace(/},/g, "\n\t},");
                str            = str.replace(/":"/g, "\" : \"");
                str            = str.replace(/":/g, "\" : ");
                return         str;
            },

            array: () => {
                return Object.keys(obj).map( (key) => {
                    return { [key]: obj[key] };
                });
            },

            arrayV: (string) => {
                let input = string ? string : str;

                console.log("arrayv input: " + input);

                str            = input.replace(/{/g, "array (\n\t");
                str            = str.replace("\Warray\W(\n\t", " array (\n\t\t");
                str            = str.replace(/:{/g, " => (\n\t");
                str            = str.replace(/,"/g, ",\n\t\"");
                str            = str.replace(/},/g, "\n\t),");
                str            = str.replace(/}/g, "\n)");
                str            = str.replace(/":"/g, "\" => \"");
                str            = str.replace(/":/g, "\" => ");
                return         str;
            },

             arrayToJson: (string) => {
                 let input = string ? string : str;
                 //   console.log(input);s
                 str            = input.replace(/array/g, "");
                 str            = str.replace(/\(/g, "{");
                 str            = str.replace(/ => \(/g, ":{");
                // str            = str.replace(/,"/g, ",\n\t\"");
                 str            = str.replace(/\)/g, "}");
              //   str            = str.replace(/}/g, "\n)");
                 str            = str.replace(/" => "/g, ":");
              //   str            = str.replace(/":/g, "\" => ");
                 return         str;
             }
        };
    };
}
