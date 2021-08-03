<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      ServerConfig.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   ServerConfig.vue
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
    /* nothing to see here! */
</style>
<template>
    <div>
        <table class="bordered">
            <tr>
                <th class="bb" v-text="showLanguage('users', 'host')"></th><td>{{ user.host }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'port')"></th><td>{{ user.port }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'username')"></th><td>{{ user.username }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'created')"></th><td>{{ user.created_at}}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'actions')"></th><td>
                <span class="pma-link" @click="$emit('edit-db-user', [user.id, 'db'])" v-text="showLanguage('global', 'edit')"></span> |
                <span class="pma-link-danger" @click="$emit('remove-db-user', [user.id, 'db'])" v-text="showLanguage('global', 'delete')"></span></td>
            </tr>
        </table>
    </div>
</template>
<script>
    /*
     * Import the Event bus - not being used...
     */
    //import { EventBus } from '../../../event-bus.js';

    export default {
        name: "DatabaseUser",

        /*
         *  One prop is better than none!
         */
        props: ['user'],

        /*
         *  The lonely data element
         */
        data() {
            return {
                activate: null
            }
        },

        /*
         *  Are your methods changing the world?
         */
        methods: {
            /*
             *   Calls the Translation and Language service
             */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
             *  Display a readable value
             */
            showBool( value ) {
                if (value === 1 || value === "1") {
                    return 'True';
                }
                return 'False';
            },

            /*
             *  Send the 'activate user' event
             */
            activateUser(id) {
                if (this.activate === true) {
                    this.$emit('activate-user', id);
                }
            }
        }
    }
</script>
