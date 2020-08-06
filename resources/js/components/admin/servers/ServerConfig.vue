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
                <th class="bb" v-text="showLanguage('servers', 'host')"></th><td>{{ server.host }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('servers', 'port')"></th><td>{{ server.port }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('servers', 'username')"></th><td>{{ server.username }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('servers', 'password')"></th><td>*****</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('servers', 'active')"></th>
                <td>{{ showBool(server.active) }}
                <span class="activate-checkbox" v-show="server.active == 0" :title="showLanguage('title' , 'activeServerTitle')">
                    <input @change="activateServer(server.id)" v-model="activate" type="checkbox" /> <span v-text="showLanguage('servers', 'check')"></span>
                </span>
                </td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('servers', 'created')"></th><td>{{ server.created_at}}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('servers', 'actions')"></th><td>
                <span class="pma-link" @click="$emit('edit', server.id)" v-text="showLanguage('servers', 'edit')"></span> |
                <span class="pma-link-danger" @click="$emit('delete', server.id)" v-text="showLanguage('servers', 'delete')"></span></td>
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
        /*
         *  One prop is better than none!
         */
        props: ['server'],

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
             *  Send the 'activate server' event
             */
            activateServer(id) {
                if (this.activate === true) {
                    this.$emit('activateServer', id);
                }
            }
        }
    }
</script>
