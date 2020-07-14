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

