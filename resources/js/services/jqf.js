/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      jqf.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   jqf.js
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
        if ( options.jqf )
            this.$jqf = options.jqf;

        else if ( options.parent && options.parent.$jqf )
            this.$jqf = options.parent.$jqf;
    }
});

/**
 * Class handling function to mock jQuery behavior
 *
 */
export default function makeJqf() {
    /**
     * Returns a set of methods for manipulating classes and actions on DOM elements
     */
    return function jqf(element) {
        // many methods here will require the 'list' reference
        let list = element ? element.classList : null, text;

        return {
            toggle: function (c) {
                list.toggle(c);
                return this
            },
            add: function (c) {
                list.add(c);
                return this
            },
            remove: function (c) {
                list.remove(c);
                return this
            },
            replace: function (array) {
                list.remove(array[0]);
                list.add(array[1]);
                return this
            },
            hasClass: function (c) {
                return list.forEach(function(value) {
                    if (value === c) {
                        return true
                    }
                });
            },
            text: function (t) {
                element.innerText = t;
                return this
            },
            html: function (h, nl2br) {
                if (nl2br) {
                    h = this.nl2br(h);
                }
                element.innerHTML = h;
                return this
            },
            focus: function () {
                element.focus();
                return this
            },
            show: function () {
                element.style.display = 'inline-block';
                return this
            },
            hide: function () {
                element.style.display = 'none';
                return this
            },
            css: function (e, v) {
                element.style[e] = v;
                return this
            },
            value: function () {
                if (element.value) {
                    return element.value
                }
                return element.options[element.selectedIndex].value
            },
            readonly: function (s) {
                if (s === true) {
                    element.readonly = 'readonly'
                }
                else {
                    element.readonly = false
                }
                return this
            },
            nl2br: function (str) {
                return str ? str.replaceAll("\n", "<br>") : '';
            },
            humanReadable: function(bytes, precision = 3) {
                if (bytes === 0) {
                    return 0;
                }
                if (bytes < 1024) {
                    return Math.round(bytes, 2) + "B";
                }
                if (bytes < 1024 * 1024) {
                    return Math.round(bytes/1024, precision) + "k";
                }
                if (bytes < 1024 * 1024 * 1024) {
                    return Math.round(bytes/1024/1024, precision) + "m";
                }
                if (bytes < 1024 * 1024 * 1024 * 1024) {
                    return Math.round(bytes/1024/1024/1024, precision) + "g";
                }
                return bytes
            }
        }
    }
}
