<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      Command.vue 1001 28/9/20, 10:17 pm  Gilbert Rehling $
  - @package      PhpMongoAdmin\resources
  - @subpackage   Command.vue
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
    /* @import '~@/abstracts/_variables.scss'; */
</style>

<template>
    <div id="pma-command" class="database-inner align-left" v-if="show">
        <div class="title">
            <h3 v-text="showLanguage('command', 'title')"></h3>
        </div>
        <p class="top-link"><a href="http://docs.mongodb.org/manual/reference/command/" target="_blank" v-text="showLanguage('command', 'reference')"></a></p>
        <form class="panel-form">
            <textarea v-model="form.command"></textarea>
            <select v-model="form.database">
                <option v-for="(db, index) in getDatabases"  :key="index" v-bind:database="db" :value="db.db.name">{{ db.db.name }}</option>
            </select>
            <select v-model="form.format">
                <option value="json" v-text="showLanguage('command', 'json')"></option>
                <option value="array" v-text="showLanguage('command', 'array')"></option>
            </select>
            <br>
            <button class="button" v-on:click="sendCommand" v-text="showLanguage('command', 'execute')"></button>
            <button class="button warning" v-on:click="clear" v-text="showLanguage('global', 'clear')"></button>
        </form>
        <p v-show="errorMessage || message">
            <span class="msg">
                <span class="error">{{ errorMessage }}</span>
                <span class="action">{{ message }}</span>
            </span>
        </p>
        <div v-if="results">
            <div v-for="(result, key) in results" v-bind:result="result" v-bind:key="key">
                <p v-html="highlight(key, result)"></p>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../../../../event-bus";

    export default {
        name: "Command",

        /*
         *  Component data container
         */
        data() {
            return {
                databases: null,
                errorMessage: null,
                form: {
                    command: '{\n\t"listCommands": 1\n}',
                    database: null,
                    format: 'json'
                },
                index: 0,
                limit: 55,
                message: null,
                results: null,
                key: null,
                show: false
            }
        },

        computed: {
            getDatabases() {
                return this.$store.getters.getDatabases;
            },

            watchActiveDatabase() {
                return this.$store.getters.getActiveDatabase;
            }
        },

        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str);
                }
                return this.$store.getters.getLanguageString( context, key );
            },

            getActiveDatabase() {
                this.form.database = this.$store.getters.getActiveDatabase;
            },

            clear() {
                this.errorMessage = '';
                this.form.database = null;
                this.results = null;
            },

            sendCommand() {
                if (this.form.command) {
                    let command = this.$convObj().minify( this.form.command );
                    let data = {
                        database: this.form.database,
                        params: {
                            format: this.form.format,
                            command: command,
                            database: this.form.database
                        }
                    };
                    this.$store.dispatch('databaseCommand', data);
                    this.handleCommand();
                }
            },

            handleCommand() {
                let status = this.$store.getters.getCommandLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleCommand();
                    }, 250);
                }
                if (status === 2) {
                    let results  = this.$store.getters.getCommandResults,
                        x;
                    for (x in results) {
                        this.results = results[x];
                        this.key = x;
                        break;
                    }
                }
                if (status === 3) {
                    this.errorMessage = 'An error occurred running the command - try reformatting the query';
                    this.results = this.$store.getters.getErrorData;
                }
            },

            highlight( command, input ) {
                let object = { [command] : input };
                input = JSON.stringify(object);
                input = input.replace('false', '~');
                input = input.replace('true', '`');
                return this.$convObj().jsonH( input );
            },

            /*
            *   Show component
            */
            showComponent() {
                this.show = true;
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false;
            }
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent();

            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent();

            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-command', () => {
                this.showComponent();

            });
        },

        watch: {
            watchActiveDatabase() {
                this.getActiveDatabase()
            }
        }
    }
</script>
