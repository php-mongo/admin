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
