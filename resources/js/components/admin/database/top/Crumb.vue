<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Crumb.vue 1001 6/8/20, 8:58 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Crumb.vue
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
    /* nothing to see here */
</style>

<template>
    <li class="crumb-link text-left" v-if="crumb.name !== null">
        <span class="dbl-arr">>></span> <img :src="'/img/icon/' + getCrumbType + '.png'" />
        <span v-if="crumb.link === true" class="crumb pma-link" @click="$emit('loadCrumb', crumb.name)">{{getCrumbName(crumb)}}</span>
        <span v-if="crumb.link === false" class="pma-text">{{getCrumbName(crumb)}}</span>
    </li>
</template>

<script>
    export default {
        /*
        *   The component accepts one crumb as a property
        */
        props: ['crumb'],

        data() {
            return {
                name: 'n/a',
                type: null,
                link: false,
                option: null
            }
        },

        computed: {
            getCrumbType() {
                return this.type;
            }
        },

        methods: {

            getCrumbName( crumb ) {
                if (crumb) {
                    if (crumb.name) {
                        this.name = crumb.name;
                        this.option = crumb.option
                    }
                    if (crumb.type) {
                        this.type = crumb.type;
                    }
                }
                return this.name;
            }
        }
    }
</script>
