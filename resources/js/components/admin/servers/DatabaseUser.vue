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
    .users {
        .to-lower {
            text-transform: lowercase;
        }
        .user-collapse {
            cursor: pointer;
        }
        th.bb {
            width: 130px;
        }
    }
</style>
<template>
    <div>
        <p class="form-error" v-show="hasMessage">{{ user.message }}</p>
        <table class="bordered users">
            <tr>
                <th class="bb" v-text="showLanguage('users', 'id')"></th>
                <td v-on:click="collapseUser">
                    {{ user._id }}
                    <span class="user-collapse u-pull-right" v-show="collapsed" :title="showLanguage('users', 'collapse')"><img src="img/sort-asc-dark.svg" alt="Collapse user" /> </span>
                    <span class="user-collapse u-pull-right" v-show="!collapsed" :title="showLanguage('users', 'expand')"><img src="img/sort-desc-dark.svg" alt="Expand user" /> </span>
                </td>
            </tr>
            <tr v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'userId')"></th><td>{{ user.userId }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'username')"></th><td>{{ user.user }}</td>
            </tr>
            <tr v-for="(role, index) in user.roles" :key="index" v-bind:role="role" v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'role')"></th>
                <td class="text-center">
                    <span class="to-lower" v-text="showLanguage('database', 'db')"></span>: {{ role.db }},
                    <span class="to-lower" v-text="showLanguage('users', 'role')"></span>: {{ role.role }}</td>
            </tr>
            <tr v-show="!hasRootRole" >
                <th class="bb" v-text="showLanguage('users', 'actions')"></th>
                <td>
                    <span
                        class="pma-link"
                        @click="$emit('edit-user', [user._id, 'database', user._id])"
                        v-text="showLanguage('global', 'edit')"
                    ></span> |
                    <span
                        class="pma-link-danger"
                        @click="$emit('delete-user', [user._id, 'database', user._id])"
                        v-text="showLanguage('global', 'delete')"
                    ></span>
                    <span class="doc-right-to-top">
                        <span
                            class="pma-link"
                            v-text="showLanguage('document', 'top')"
                            @click="$emit('pma-main-panel-scroll', {})"
                        ></span>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</template>
<script>
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
                activate: null,
                collapsed: true,
            }
        },

        computed: {
            hasRootRole() {
                return this.user.roles.find(role => role.role === 'root')
            },

            hasMessage() {
                return (this.user.message)
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

            collapseUser() {
                this.collapsed = !this.collapsed
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
        },
    }
</script>
