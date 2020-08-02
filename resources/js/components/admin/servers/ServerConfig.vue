<style lang="scss">
    /* nothing to see here! */
</style>
<template>
    <div>
        <table class="bordered">
            <tr>
                <th class="bb">Host</th><td>{{ server.host }}</td>
            </tr>
            <tr>
                <th class="bb">Port</th><td>{{ server.port }}</td>
            </tr>
            <tr>
                <th class="bb">Username</th><td>{{ server.username }}</td>
            </tr>
            <tr>
                <th class="bb">Password</th><td>*****</td>
            </tr>
            <tr>
                <th class="bb">Active</th><td>{{ showBool(server.active) }} <span class="activate-checkbox" v-show="server.active == 0" title="Check to activate this server"><input @change="activateServer(server.id)" v-model="activate" type="checkbox" /> Check to activate</span></td>
            </tr>
            <tr>
                <th class="bb">Created</th><td>{{ server.created_at}}</td>
            </tr>
            <tr>
                <th class="bb">Actions</th><td><span class="pma-link" @click="$emit('edit', server.id)">Edit</span> | <span class="pma-link-danger" @click="$emit('delete', server.id)">Delete</span></td>
            </tr>
        </table>
    </div>
</template>
<script>
    /*
     * Import the Event bus
     */
    import { EventBus } from '../../../event-bus.js';

    export default {
        props: ['server'],

        data() {
            return {
                activate: null
            }
        },

        methods: {
            /*
             *   Calls the Translation and Language service
             */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
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

            /*
             *  Send the 'activate server' event
             */
            activateServer(id) {
                if (this.activate === true) {
                    this.$emit('activateServer', id);
                }
            }
        }
    }
</script>
