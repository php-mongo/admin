<style lang="scss">
    /* nothing to see here */
</style>

<template>
    <li class="crumb-link text-left">
       <span class="dbl-arr">>></span> <img :src="'/img/icon/' + getCrumbType + '.png'" /> <span class="crumb pma-link" @click="$emit('loadCrumb', crumb.name)">{{getCrumbName(crumb)}}</span>
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
                type: null
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
