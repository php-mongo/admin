<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Transfer.vue 1001 28/9/20, 10:14 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Transfer.vue
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

<style lang="scss">
    /* @import '~@/abstracts/_variables.scss'; */

</style>

<template>
    <div id="pma-transfer" class="pma-transfer align-left" v-if="show">
        <form class="command-form">
            <button class="button" v-on:click="runTransfer" v-text="showLanguage('transfer', 'transfer')"></button>
        </form>
        <p v-show="errorMessage || message">
            <span class="msg">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ message }}</span>
            </span>
        </p>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Transfer",

        /*
         *  Component data container
         */
        data() {
            return {
                collections: [],
                errorMessage: null,
                form: {
                    authenticate: null,
                    collections: [],
                    database: null,
                    format: 'json',
                    host: null,
                    indexes: false,
                    password: null,
                    port: 27017,
                    socket: null,
                    username: null,
                },
                index: 0,
                limit: 55,
                message: null,
                results: null,
                key: null,
                show: false
            }
        },

        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   Show component
            */
            showComponent() {
                this.show = true;
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false;
            },

            runTransfer() {
                console.log(this.form);
            }
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();

            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();

            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-transfer', () => {
                this.showComponent();

            });
        },
    }
</script>
