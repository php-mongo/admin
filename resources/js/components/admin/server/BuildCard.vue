<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      BuildCard.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   BuildCard.vue
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
</style>
<template>
    <tr v-html="getRow"></tr>
</template>
<script>
    export default {
        /*
         *   We are passing 2 props to this card - one is the key value (index)
         */
        props: [
            'index',
            'value'
        ],

        /*
         *   Dr Smith! It does not compute!
         */
        computed: {
            getRow() {
                let html = '';
                if (this.isObject(this.value)) {
                    html = '<td class="server-info rb">' +  this.index + '</td>';
                    let row = this.value,
                        obj =  '',
                        x;
                    for (x in row) {
                        obj += x + ':' + row[x] +', ';
                    }
                    // in case its an empty array
                    if (obj === '') { obj = '[]'; }
                    html = html + '<td class="server-info rb">' + obj + '</td>';
                    return html;
                }
                else {
                    html = '<td class="server-info rb">' +  this.index + '</td>'
                        + '<td class="server-info">' + this.checkUndefined(this.value) + '</td>';
                    return html;
                }
            }
        },

        /*
         *   Methods for our Buildinfo card
         */
        methods: {
            checkUndefined(val) {
                // handle false
                if (val === false) {
                    return 'false';
                }
                // handle empty array !! probably never runs here !!
                if (val.length === 0) {
                    return '[]';
                }
                // handle undefined
                if (!val) {
                    return 0;
                }
                return val;
            },

            /*
             *   Simple object check - should match for Objects & Arrays
             */
            isObject: function(o) {
                return typeof o === "object";
            }
        }
    }
</script>
