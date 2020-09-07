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
         let str  = null;
         // we basically want to deal with 'strings' here
         if (obj && type === 'object') {
             str = JSON.stringify( obj );
             // ToDo !! this may be ovekill ?? !!
             str = str.replace("\"_id\":null,", "");
             // console.log("convObj str: " + str);
         }
         else if (obj) {
             // if the 'obj' is passed as a string, just pass it on
             str = obj;
         }

         return {
             json: (input) => {
                return str ? str : JSON.stringify( input );
             },

             jsonV: (string) => {
                 str = string ? string : str;
                // console.log("jsonV input: " + str);

                 let openB   = 0;  // track open braces & brackets ( { & [ )
                 let i       = 0;  // indexing
                 let x       = null; // its the x-factor
                 let divArea = '';
                 let tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
                 for (x in str) {
                     let plus = (parseInt(x) + 1);

                     if (str[x] === '{' || str[x] === '[') {
                         openB += 1;
                         divArea += str[x] + '<br>';
                         for (i = 0; i < openB; i += 1) {
                             divArea += tab;
                         }
                     }
                     else if (str[x] === '}' || str[x] === ']') {
                         divArea += '<br>';

                         if (openB > 0) {
                             openB -= 1;
                         }
                         for (i = 0; i < openB; i += 1) {
                             divArea += tab;
                         }
                         divArea += str[x];
                     }
                     else if (str[x] === ',') {
                         divArea += str[x] + '<br>';

                         for (i = 0; i < openB; i += 1) {
                             divArea += tab;
                         }
                     }
                     else {
                         divArea += str[x];
                     }
                 }
                 return divArea;
             },

             jsonT: (string) => {
                 str = string ? string : str;//
                 // console.log("jsonT input: " + str);

                 let openB    = 0;  // track open braces & brackets ( { & [ )
                 let textArea = ''; // target for textarea output
                 let i        = 0;  // indexing
                 let x        = null; // its the x-factor
                 for (x in str) {
                     if (str[x] === '{' || str[x] === '[') {
                         openB += 1;
                         textArea += str[x] + '\n';
                         for (i = 0; i < openB; i += 1) {
                             textArea += '\t';
                         }
                     }
                     else if (str[x] === '}' || str[x] === ']') {
                         textArea += '\n';

                         if (openB > 0) {
                             openB -= 1;
                         }
                         for (i = 0; i < openB; i += 1) {
                             textArea += '\t';
                         }
                         textArea += str[x];
                     }
                     else if (str[x] === ',') {
                         textArea += str[x] + '\n';

                         for (i = 0; i < openB; i += 1) {
                             textArea += '\t';
                         }
                     }
                     else {
                         textArea += str[x];
                     }
                 }
                 return textArea;
             },

             jsonH: (string) => {
                 str = string ? string : str;//
                 // console.log("jsonT input: " + str);

                 let divAreaH = ''; // target for div area output
                 // divAreaH  = str.replace('ObjectId', )
                 let openB    = 0;  // track open braces & brackets ( { & [ )
                 let tab      = "&nbsp;&nbsp;&nbsp;&nbsp;";
                 let i        = 0;  // indexing
                 let x        = null;
                 for (x in str) {
                     let plus = (parseInt(x) + 1);

                     if (str[x] === '{' || str[x] === '[') {
                         openB += 1;
                         divAreaH += '<span style="color: green">' + str[x] + '</span>' + '<br>';
                         for (i = 0; i < openB; i += 1) {
                             divAreaH += tab;
                         }
                     }
                     else if (str[x] === '}' || str[x] === ']') {
                         divAreaH += '<br>';

                         if (openB > 0) {
                             openB -= 1;
                         }
                         for (i = 0; i < openB; i += 1) {
                             divAreaH += tab;
                         }
                         divAreaH += '<span style="color: green">' + str[x] + '</span>';
                     }
                     else if (str[x] === '(' || str[x] === ')') {
                         divAreaH += '<span style="color: blue">' + str[x] + '</span>';
                     }
                     else if (str[x] === ',') {
                         divAreaH += '<span style="color: green">' + str[x] + '</span>' + '<br>';

                         for (i = 0; i < openB; i += 1) {
                             divAreaH += tab;
                         }
                     }
                     else if (str[x] === ':') {
                         divAreaH += '<span class="colon" style="color: green">' + str[x] + '</span>';
                     }
                     else if (str[x] === '"') {
                         divAreaH += '<span style="color: #800000">' + str[x] + '</span>';
                     }
                     else {
                         divAreaH += '<span style="color: red">' + str[x] + '</span>';
                     }
                 }

                 return divAreaH;
             },

             array: () => {
                return Object.keys(obj).map( (key) => {
                    return { [key]: obj[key] };
                });
             },

             arrayV: (string) => {
                 str = string ? string : str;
                 // console.log("arrayV input: " + str);

                 let openB    = 0;  // track open braces & brackets ( { & [ )
                 let i        = 0;  // indexing
                 let x        = null; // its the x-factor
                 let divArray = '';
                 let tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
                 for (x in str) {
                     if (str[x] === '{' || str[x] === '[') {
                         openB += 1;
                         divArray += 'array(<br>';
                         for (i = 0; i < openB; i += 1) {
                             divArray += tab;
                         }
                     }
                     else if (str[x] === '}' || str[x] === ']') {
                         divArray += '<br>';

                         if (openB > 0) {
                             openB -= 1;
                         }
                         for (i = 0; i < openB; i += 1) {
                             divArray += tab;
                         }
                         divArray += ')';
                     }
                     else if (str[x] === ',') {
                         divArray += str[x] + '<br>';

                         for (i = 0; i < openB; i += 1) {
                             divArray += tab;
                         }
                     }
                     else if (str[x] === ":") {
                         divArray += ' => ';
                     }
                     else {
                         divArray += str[x];
                     }
                 }
                 return divArray;
             },

             arrayT: (string) => {
                 str = string ? string : str;
                 // console.log("arrayT input: " + str);

                 let openB     = 0;  // track open braces & brackets ( { & [ )
                 let textArray = '';
                 let i         = 0;  // indexing
                 let x         = null; // its the x-factor
                 for (x in str) {
                     if (str[x] === '{' || str[x] === '[') {
                         openB += 1;
                         textArray += 'array(\n';
                         for (i = 0; i < openB; i += 1) {
                             textArray += '\t';
                         }
                     } else if (str[x] === '}' || str[x] === ']') {
                         textArray += '\n';

                         if (openB > 0) {
                             openB -= 1;
                         }
                         for (i = 0; i < openB; i += 1) {
                             textArray += '\t';
                         }
                         textArray += ')';
                     } else if (str[x] === ',') {
                         textArray += str[x] + '\n';

                         for (i = 0; i < openB; i += 1) {
                             textArray += '\t';
                         }
                     } else if (str[x] === ":") {
                         textArray += ' => ';
                     } else {
                         textArray += str[x];
                     }
                 }
                 return textArray;
             },

             arrayH: (string) => {
                 str = string ? string : str;//
                 // console.log("jsonT input: " + str);

                 let openB     = 0;  // track open braces & brackets ( { & [ )
                 let divArrayH = '';
                 let tab       = "&nbsp;&nbsp;&nbsp;&nbsp;";
                 let i         = 0;  // indexing
                 let x         = null; // its the x-factor
                 for (x in str) {
                     if (str[x] === '{' || str[x] === '[') {
                         openB += 1;
                         divArrayH += '<span style="color: blue">array(</span><br>';
                         for (i = 0; i < openB; i += 1) {
                             divArrayH += tab;
                         }
                     }
                     else if (str[x] === '}' || str[x] === ']') {
                         divArrayH += '<br>';

                         if (openB > 0) {
                             openB -= 1;
                         }
                         for (i = 0; i < openB; i += 1) {
                             divArrayH += tab;
                         }
                         divArrayH += '<span style="color: blue">)</span>';
                     }
                     else if (str[x] === ',') {
                         divArrayH += '<span style="color: green">' + str[x] + '</span>' + '<br>';

                         for (i = 0; i < openB; i += 1) {
                             divArrayH += tab;
                         }
                     }
                     else if (str[x] === '(' || str[x] === ')') {
                         divArrayH += '<span style="color: green">' + str[x] + '</span>';
                     }
                     else if (str[x] === ":") {
                         divArrayH += ' <span style="color: green">=></span> ';
                     }
                     else if (str[x] === '"') {
                         divArrayH += '<span style="color: #800000">' + str[x] + '</span>';
                     }
                     else {
                         divArrayH += '<span style="color: red">' + str[x] + '</span>';
                     }
                 }
                 return divArrayH;
             },

             arrayToJson: (string) => {
                 let input = string ? string : str;
                 //   console.log(input);
                 str            = input.replace(/array/g, "");
                 str            = str.replace(/\(/g, "{");
                 str            = str.replace(/ => \(/g, ":{");
                 str            = str.replace(/\)/g, "}");
                 str            = str.replace(/=>/g, ":");
                 return         str;
             },

             minify: (string) => {
                 let input = string ? string : str;
                 //   console.log(input);
                 let doc = input.replace(/\n/g, ""); // remove new line
                 doc = doc.replace(/\t/g, ""); // remove tabs
                 // doc = doc.replace(/\s/g, ""); // removing all space was a bad idea
                 doc = doc.replace(/\s\s/g, " ");
                 return doc;
             },

             typeOf: (string) => {
                 let value = string ? string : str;
                 let s = typeof value;
                 if (s === 'object') {
                     if (value) {
                         if (Object.prototype.toString.call(value) === '[object Array]') {
                             s = 'array';
                         }
                     } else {
                         s = 'null';
                     }
                 }
                 return s;
             },
             isEmpty: function (string) {
                 let o = string ? string : str;
                 let i, v, t = this.typeOf(o);
                 if (t === 'object') {
                     for (i in o) {
                         v = o[i];
                         if (v !== undefined && this.typeOf(v) !== 'function') {
                             return false;
                         }
                     }
                 }
                 if (t === 'array') {
                     if (t.length >= 1) {
                         return false;
                     }
                 }
                 return true;
             }
        };
    };
}
