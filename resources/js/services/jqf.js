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
        let list = element.classList;

        return {
            toggle: function (c) {
                list.toggle(c);
                return this;
            },
            add: function (c) {
                list.add(c);
                return this;
            },
            remove: function (c) {
                list.remove(c);
                return this;
            },
            replace: function (array) {
                list.remove(array[0]);
                list.add(array[1]);
                return this;
            },
            hasClass: function (c) {
                console.log(list);
                console.log(c);
                return list.forEach(function(value, index) {
                    console.log("checking: " + value);
                    if (value === c) {
                        console.log("found it!!");
                        return true;
                    }
                });
            },
            text: function (t) {
                element.innerText = t;
            },
            html: function (h) {
                element.innerHTML = h;
            },
            focus: function () {
                element.focus();
            },
            show: function () {
                element.style.display = 'inline-block';
            },
            hide: function() {
                element.style.display = 'none';
            },
            css: function(e, v) {
                element.style[e] = v;
            },
            value: function() {
                if (element.value) {
                    return element.value;
                }
                return element.options[element.selectedIndex].value;
            }
        };
    }
}
