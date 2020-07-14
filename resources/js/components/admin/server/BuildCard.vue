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
