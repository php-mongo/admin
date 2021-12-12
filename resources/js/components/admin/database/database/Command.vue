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
            <button class="button warning" v-on:click="reset" v-text="showLanguage('global', 'clear')"></button>
        </form>
        <p v-show="errorMessage || message">
            <span class="msg">
                <span class="action">{{ message }}</span>
                <span class="error">{{ errorMessage }}</span>
            </span>
        </p>
        <div v-if="results && typeof results === 'object'">
            <div v-for="(data, key) in results" v-bind:data="data" v-bind:key="key">
                <p v-html="highlight(key, data)"></p>
            </div>
        </div>
        <div v-if="results && typeof results !== 'object'">
            <div v-bind:results="results" v-bind:key="key">
                <p>{{ results }}</p>
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
                return this.$store.getters.getDatabases
            },

            watchActiveDatabase() {
                return this.$store.getters.getActiveDatabase
            }
        },

        methods: {
            /*
            *   Calls the Translation and Language service
            */
            showLanguage( context, key, str ) {
                if (str) {
                    return this.$store.getters.getLanguageString( context, key ).replace("%s", str)
                }
                return this.$store.getters.getLanguageString( context, key )
            },

            getActiveDatabase() {
                this.form.database = this.$store.getters.getActiveDatabase
            },

            reset() {
                this.message = '';
                this.errorMessage = '';
                this.results = null;
                this.form = {
                    command: '{\n\t"listCommands": 1\n}',
                    database: null,
                    format: 'json'
                }
            },

            clear() {
                this.message = '';
                this.errorMessage = '';
                this.results = null
            },

            validate() {
                let command = this.form.command;
                if (command && command.replace(/\s/g, "") !== '{}')  {
                    if (this.form.database) {
                        if (command.indexOf(':') !== -1) {
                            let split = command.split(":");
                            if (split[1].replace(/\s/g,"").replace("}", "").length >= 1) {
                                return true
                            }
                            return this.returnError('A command must have at least 1 parameter. "command-name" : "parameter')
                        }
                        return this.returnError('A command must have at least 2 parts separated by a colon. "command-name" : 1')
                    }
                    return this.returnError(this.showLanguage('command', 'selectDb'))
                }
                return this.returnError('No command provided')
            },

            sendCommand() {
                this.clear();
                if (this.validate()) {
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
                    this.handleCommand()
                }
            },

            handleCommand() {
                let status = this.$store.getters.getCommandLoadStatus;
                if (status === 1 && this.index < this.limit) {
                    this.index += 1;
                    setTimeout(() => {
                        this.handleCommand()
                    }, 250)
                }
                if (status === 2) {
                    this.results = this.$store.getters.getCommandResults
                }
                if (status === 3) {
                    this.message = 'An error occurred running the command: try reformatting the query, ensure all braces and brackets are closed';
                    this.errorMessage = this.$store.getters.getErrorData
                }
            },

            highlight( key, data ) {
                let object = { [key] : data };
                data = JSON.stringify(object);
                data = data.replace('false', '~');
                data = data.replace('true', '`');
                return this.$convObj().jsonH( data )
            },

            /*
            *   Show component
            */
            showComponent() {
                this.show = true
            },

            /*
            *   Hide component
            */
            hideComponent() {
                this.show = false
            }
        },

        mounted() {
            /*
            *    Hide this component
            */
            EventBus.$on('hide-panels', () => {
                this.hideComponent()
            });

            /*
            *    Hide this component
            */
            EventBus.$on('hide-database-panels',() => {
                this.hideComponent()
            });

            /*
            *    Show this component
            */
            EventBus.$on('show-database-command', () => {
                this.showComponent()
            });
        },

        watch: {
            watchActiveDatabase() {
                this.getActiveDatabase()
            }
        }
    }
</script>
