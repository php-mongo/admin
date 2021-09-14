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
    users {
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
        <p class="form-error" v-show="hasMessage">{{ hasMessage }}</p>
        <p class="text-info" v-show="hasMessage">{{ hasSuccess }}</p>
        <table class="bordered users">
            <tr>
                <th class="bb" v-text="showLanguage('users', 'name')"></th>
                <td v-on:click="collapseUser">
                    {{ user.name }}
                    <span class="user-collapse u-pull-right" v-show="collapsed" :title="showLanguage('users', 'collapse')">
                        <img src="img/sort-asc-dark.svg" alt="Collapse user" />
                    </span>
                    <span class="user-collapse u-pull-right" v-show="!collapsed" :title="showLanguage('users', 'expand')">
                        <img src="img/sort-desc-dark.svg" alt="Expand user" />
                    </span>
                </td>
            </tr>
            <tr v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'email')"></th>
                <td>{{ user.email }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'username')"></th>
                <td>{{ user.user }}</td>
            </tr>
            <tr v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'adminUser')"></th>
                <td>{{ showBool(user.admin_user) }}</td>
            </tr>
            <tr v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'controlUser')"></th>
                <td>{{ showBool(user.control_user) }}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'active')"></th>
                <td>
                    {{ showBool(user.active) }}
                    <span class="activate-checkbox" v-show="showActivate" :title="showLanguage('title' , 'activateUserTitle')">
                        <input @change="$emit('activate', [user.id, 'login', user.user, user.active])" type="checkbox">
                        <span v-text="showLanguage('users', 'check')"></span>
                    </span>
                </td>
            </tr>
            <tr v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'created')"></th>
                <td>{{ user.created_at}}</td>
            </tr>
            <tr v-show="!collapsed">
                <th class="bb" v-text="showLanguage('users', 'updated')"></th>
                <td>{{ user.updated_at}}</td>
            </tr>
            <tr>
                <th class="bb" v-text="showLanguage('users', 'actions')"></th>
                <td>
                    <span class="pma-link"
                          @click="$emit('edit-user', [user.id, 'login', user.user])"
                          v-text="showLanguage('global', 'edit')"
                    ></span> |
                    <span
                        v-if="user.id !== account.id"
                        class="pma-link-danger"
                        @click="$emit('delete-user', [user.id, 'login', user.user])"
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
        name: "LoginUser",

        /*
         *  One prop is better than none!
         */
        props: ['user','account'],

        data() {
            return {
                collapsed: true
            }
        },

        computed: {
            hasMessage() {
                return (this.user.message)
            },

            setMessage() {
                return this.user.message && this.user.message.indexOf('Success:') === -1 ? this.user.message : '';
            },

            hasSuccess() {
                return this.user.message && this.user.message.indexOf('Success:') === -1 ? '' : this.user.message;
            },

            showActivate() {
                return this.user.active === "0"
            },
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
        }
    }
</script>
