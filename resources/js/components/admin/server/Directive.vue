<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Directive.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Directive.vue
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
    /* no style here - inherited from parent */
    .center td {
        text-align: center !important;
    }
</style>
<template>
    <tr v-html="getRow" class="center"></tr>
</template>
<script>
    export default {
        /*
        *   Single prop for this component
        */
        props: ['directive'],

        /*
        *   Computed methods for the Directive card
        */
        computed: {
            getRow() {
                let html = '';
                let x;
                for (x in this.directive) {
                    html = '<td class="server-info rb">' + x + '</td>'
                    + '<td class="server-info rb">' + this.checkUndefined(this.directive[x].global) + '</td>'
                    + '<td class="server-info rb">' + this.checkUndefined(this.directive[x].local) + '</td>'
                    + '<td class="server-info rb">' + this.checkUndefined(this.directive[x].access) + '</td>';
                }
                return html;
            }
        },

        /*
        * Only one method to check for undefined values - so we dont show 'undefined'
        */
        methods: {
            checkUndefined(val) {
                if (!val) {
                    return 0;
                }
                return val;
            }
        }
    }
</script>

