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
        .then(r => {
            language = store.getters.getLanguageArray;
        });

    /**
    *   Returns the text language string
    */
    return function trans( context, key ) {
        return language[context][key];
    }
}
