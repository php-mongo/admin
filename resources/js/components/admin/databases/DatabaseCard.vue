<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    ul.collections {
        list-style: none;
        display: block;
        margin-left: 20px;
    }

    .hide-list {
        display: none !important;
    }
</style>

<template>
    <tr v-if="db">
        <td class="info rb"><input v-show="db.db.name !== 'admin' && db.db.name !== 'local'" v-on:click="selectCheckbox" :checked="isChecked" type="checkbox" :value="db.db.name" /></td>
        <td class="info rb"><span class="pma-link" v-on:click="showDatabase">{{ getDbName(db) }}</span></td>
        <td class="info rb">{{ humanReadable(db.db.sizeOnDisk) }}</td>
        <td class="info rb">{{ humanReadable(db.stats.storageSize) }}</td>
        <td class="info rb">{{ humanReadable(db.stats.dataSize) }}</td>
        <td class="info rb">{{ humanReadable(db.stats.indexSize) }}</td>
        <td class="info rb text-center">{{ db.stats.collections }}</td>
        <td class="info text-center">{{ db.stats.objects }}</td>
    </tr>
</template>

<script>
    /*
    * Import the Event bus
    */
    import { EventBus } from '../../../event-bus.js';

    export default {
        /*
        *   The component accepts one db as a property
        */
        props: ['db'],

        /*
        *   We keep a reference to the associate database name
        */
        data() {
            return {
                dbs: null,
                checked: false,
                name: null
            }
        },

        /*
        * Defines the computed properties on the component.
        */
        computed: {
            /*
            *   Sets the checkbox to checked or unchecked dynamically
            */
            isChecked() {
                return this.checked;
            }
        },

        /*
        *   Defined methods for the component
        */
        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key ) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
            *   There are two versions of DB name returned
            */
            getDbName( db ) {
                if (db.db.databaseName) {
                    this.name = db.db.databaseName;
                    return db.db.databaseName;
                }
                else {db.db.name;
                    this.name = db.db.name;
                    return db.db.name;
                }
            },

            /*
            *   Load the database panel
            */
            showDatabase() {
            //    this.$store.dispatch('clearDatabase');
                this.$store.dispatch('setActiveDatabase', this.name);
                this.$store.dispatch('loadDatabase', this.name);
                EventBus.$emit('hide-panels');
                EventBus.$emit('show-database', this.name);
            },

            /*
            *   Make the byte human readable
            */
            humanReadable(bytes, precision) {
                if (bytes === 0) {
                    return 0;
                }
                if (bytes < 1024) {
                    return bytes + "B";
                }
                if (bytes < 1024 * 1024) {
                    return Math.round(bytes/1024, precision) + "k";
                }
                if (bytes < 1024 * 1024 * 1024) {
                    return Math.round(bytes/1024/1024, precision) + "m";
                }
                if (bytes < 1024 * 1024 * 1024 * 1024) {
                    return Math.round(bytes/1024/1024/1024, precision) + "g";
                }
                return bytes;
            },

            /*
            *   The status will govern the checkbox's state
            */
            checkDatabase(status) {
                this.checked = status;
            },

            /*
            *   Method handles the checkbox click
            */
            selectCheckbox() {
                this.checked = !this.checked;
            }
        },

        /*
        *   Handle mounted method requirements
        */
        mounted() {
            EventBus.$on('check-all-databases', function() {
                // exclude admin & local from deletion
                if (this.name !== 'admin' && this.name !== 'local') {
                    this.checkDatabase(true);
                }

            }.bind(this));

            EventBus.$on('uncheck-all-databases', function() {
                this.checkDatabase(false);

            }.bind(this));
        },

        /*
        *   Who watches the watchers?
        */
        watch: {
            /*
            *   ToDo: It probably would have been cleaner to pass the @click event directly to the parent
            *   But this is working so I'll leave it for now
            */
            isChecked() {
                if (this.checked === true) {
                    if (this.name !== 'admin' && this.name !== 'local') {
                        EventBus.$emit('track-checked-db', this.name );
                    }
                }
                if (this.checked === false) {
                    EventBus.$emit('untrack-checked-db', this.name );
                }
            }
        }
    }
</script>
