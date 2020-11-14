/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      translate.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   translate.js
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

import store from "../store";

Vue.mixin( {
    beforeCreate() {
        const options = this.$options;
        if ( options.trans )
            this.$trans = options.trans;

        else if ( options.parent && options.parent.$trans )
            this.$trans = options.parent.$trans;
    }
});

/**
 * Language and translation provider service
 * ToDo: implement the locale value once we have more languages to load
 *
 * @param locale
 *
 * @returns {function(*, *): *}
 */
export default function makeTrans( locale ) {
    /**
    *   Implement the translations loaded via /js/lang.js
    */
    let language;
    store.dispatch('commitLanguage', locale)
        .then(() => {
            language = store.getters.getLanguageArray;
        });

    /**
    *   Returns the text language string
    */
    return function trans( context, key ) {
        return language[context][key];
    }
}
