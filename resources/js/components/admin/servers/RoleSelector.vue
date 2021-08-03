<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      RoleSelector.vue 1001 22/11/20, 1:30 am  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   RoleSelector.vue
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
    .role-selector {
        margin-bottom: 0;

        select {
            width: auto;
        }
        button {
            margin-bottom: 0.3rem;
        }
    }
</style>

<template>
    <p class="role-selector" v-if="show">
        <select v-model="role" @change="$emit('updateRole', role)">
            <option value="" v-text="showLanguage('users', 'roleSelect')"></option>
            <option value="readAnyDatabase" v-text="showLanguage('users', 'roleReadonly')"></option>
            <option value="readWriteAnyDatabase" v-text="showLanguage('users', 'roleReadWrite')"></option>
            <option value="userAdminAnyDatabase" v-text="showLanguage('users', 'roleUserAdmin')"></option>
        </select>
        <button class="button tiny" v-text="showLanguage('users', 'roleAdd')" @click="$emit('addRole')"></button>
        <button class="button alert tiny" v-text="showLanguage('users', 'roleRemove')" @click="$emit('removeRole', role); clear"></button>
    </p>
</template>

<script>
    export default {
        name: "RoleSelector",

        props: [
            'roles'
        ],

        data() {
            return {
                role: '',
                show: true
            }
        },

        /*
         *   Define methods for the server component
         */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage(context, key) {
                return this.$store.getters.getLanguageString(context, key);
            },

            clear() {
                this.role = '';
            }
        }
    }
</script>
