<!--
  - PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
  - @version      SetupModal.vue 1001 6/8/20, 1:00 am  Gilbert Rehling $
  - @package      SetupModal.vue
  - @subpackage   Id
  - @link         https://github.com/php-mongo/admin PHP MongoDB Admin
  - @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
  - @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
  - @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
  -  php-mongo-admin - License conditions:
  -  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
  -  This web application is available as Free Software and has no implied warranty or guarantee of usability.
  -  See licence.txt for the complete licensing outline.
  -  See COPYRIGHT.php for copyright notices and further details.
  -->

<style lang="scss">
    @import '~@/abstracts/_variables.scss';

    div#register-modal {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba( 0, 0, 0, .8 );
        z-index: 7;

        div.register-box {
            width: 100%;
            max-width: 530px;
            min-width: 320px;
            background-color: transparent;
            -webkit-box-shadow: 0 1px 3px rgba(50,50,50,0.08);
            box-shadow: 0 1px 3px rgba(50,50,50,0.08);
            font-size: 16px;
            position: absolute;
            margin-top: 15px;
            top: 50%;
            left: 50%;
            height: 100%;
            transform: translate(-50%, -50%);
            z-index: 8;
            overflow: auto;
            scroll-behavior: smooth;

            div.register-label {
                color: $whiteFont;
                font-family: "Lato", sans-serif;
                font-weight: bold;
                text-transform: uppercase;
                text-align: center;
                width: 100%;
                border-radius: 10px 10px 0 0;
                padding-top: 8px;
                height: 35px;

                h3 {
                    font-size: 1.0rem;

                    img {
                        float: right;
                        margin: 1px 15px 0 0;
                        border-radius: 3px;
                        cursor: pointer;
                        border: $red dotted 1px;
                    }
                }
            }

            .rego-content {
                width: 100%;
                position: relative;
                text-align: center;

                a.learn-more-button {
                    border: 2px solid $buttonBorderedColor;
                    border-radius: 3px;
                    text-transform: uppercase;
                    font-family: "Lato", sans-serif;
                    color: $linkButtonColor;
                    width: 360px;
                    font-size: 16px;
                    text-align: center;
                    padding: 10px;
                    margin: 20px 0 0 0;
                    display: block;

                    &:hover {
                        color: $buttonColor;
                        background-color: $buttonBackgroundHeaderHover;
                    }
                }

                p {
                    position: relative;
                    margin: 0 0 10px;
                }

                label {
                    width: 170px;
                    text-align: right
                }

                span.help-block {
                    width: auto;
                    display: inline-block;
                    position: absolute;
                    padding: 5px;
                    margin-left: 20px;
                    margin-top: 7px;
                }

                span.has-success {
                    font-style: italic;
                    color: $spanHasSuccess;
                }

                button {
                    border-radius: 7px;
                }

                button.success {
                    font-weight: 600;
                }

                .has-error {
                    border: 1px solid $fieldBorderError;
                    color: $errorColor;
                }

                .form-group {
                    background-color: $formBackgroundColor;
                    padding: 15px 10px;

                    .fieldBlock {
                        display: inline-block;

                        label, input {
                            display: inline-block;
                        }

                        .form-control {
                            width: auto;
                        }

                        input, select {
                            margin: 6px 0;
                        }

                        .is-invalid {
                            color: $fieldColorError;
                        }

                        .button-box {
                            margin: 12px 0;
                        }

                        .has-success {
                            border: 1px solid $fieldBorderSuccess;
                        }
                    }
                }

                .login-button {
                    border: 2px solid $buttonBorderedColor;
                    border-radius: 3px;
                    text-transform: uppercase;
                    font-family: "Lato", sans-serif;
                    color: $cosmicColor;
                    width: 360px;
                    font-size: 16px;
                    text-align: center;
                    padding: 10px;
                    cursor: pointer;

                    &:hover {
                        color: $whiteFont;
                        background-color: $buttonBackgroundHeaderHover;
                    }
                }

                ul.requirements {
                    list-style-type: none;

                    li {
                        margin-bottom: 15px;
                        line-height: 1.5em;
                    }
                }

                .rego-footer {
                    background-color: $formBackgroundColor;
                    padding-bottom: 20px;

                    p.learn-more-description {
                        color: $midGreyColor;
                        text-align: center;
                    }

                    a.learn-more-button {
                        display: inline-block;
                        margin: 0;
                    }
                }
            }

            .rego-confirmation {
                padding: auto 20px;
            }
        }
    }

    /* Small only */
    @media screen and (max-width: 768px) {
        div#register-modal{
            div.register-box {
                width: 95%;

                a.learn-more-button {
                    width: 300px;
                }
            }
        }
    }

    /* Medium only */
    @media (min-width: 769px) and (max-width: 992px) {
        div#register-modal {
            div.register-box {
                max-width: 840px;
                min-width: 640px;

                a.learn-more-button{
                  width: 300px;
                }
            }
        }
    }

    /* Large only */
    @media (min-width: 993px) and (max-width: 2048px) {
        div#register-modal {
            div.register-box {
                max-width: 1240px;
                min-width: 1080px;

                a.learn-more-button {
                    width: 300px;
                }
            }
        }
    }
</style>

<template>
    <div id="register-modal" v-show="show" v-on:click="closeDialogOutside($event)">
        <div class="register-box">

            <div class="register-label"><h3><span v-text="showLanguage('rego', 'title')"></span><img title="Close" alt="Close" src="/img/close-icon-white.svg"></h3></div>

            <div class="rego-content">

                <form name="registerForm" id="registerForm" ref="registerForm" action="#" method="post" class="form-group" v-on:submit="registerUser($event)">

                    <p><span class="has-info" id="introMessage" v-text="showLanguage('rego', 'intro')"></span></p>

                    <p>
                        <span class="fieldBlock">
                            <label for="name" v-text="showLanguage('rego', 'name')"></label>
                            <input class="form-control" type="text" id="name" ref="name" v-model="credentials.name" v-on:keyup="countName()" v-on:blur="countName()" required>
                        </span>
                        <span class="help-block" id="nameInfo" ref="nameInfo"></span>
                    </p>

                    <p>
                        <span class="fieldBlock">
                            <label for="email" v-text="showLanguage('rego', 'email')"></label>
                            <input class="form-control" type="email" id="email" ref="email" v-model="credentials.email" v-on:keydown="verifyName()" v-on:blur="checkEmail()" required>
                        </span>
                        <span class="help-block" id="emailInfo" ref="emailInfo"></span>
                    </p>

                    <p>
                        <span class="fieldBlock">
                            <label for="password" v-text="showLanguage('rego', 'password')"></label>
                            <input class="form-control" type="password" id="password" ref="password" v-model="credentials.password" v-on:focus="verifyEmail()" v-on:keyup="countPassword()" required>
                        </span>
                        <span class="help-block" id="passwordInfo" ref="passwordInfo"></span>
                    </p>

                    <p>
                        <span class="fieldBlock">
                            <label for="passwordConfirm" v-text="showLanguage('rego', 'confirmPassword')"></label>
                            <input class="form-control" type="password" id="passwordConfirm" ref="passwordConfirm" v-model="credentials.matched" v-on:focus="verifyPassword()" v-on:keyup="comparePassword()" required>
                        </span>
                        <span class="help-block" id="passwordConfirmInfo" ref="passwordConfirmInfo"></span>
                    </p>

                    <button type="submit" :class="'button ' + submitButtonClass" :disabled="enableSubmit" v-bind:aria-disabled="submitButtonStatus" v-text="showLanguage('rego', 'registerButton')"></button><br /><br />

                    <button class="btn login-button" v-on:click="loadLogin()" v-text="showLanguage('rego', 'alreadyMember')"></button>
                </form>
                <div class="col-sm-12 form-group">
                    <h3 v-text="showLanguage('rego', 'reqTitle')"></h3>
                    <p class="text-info" v-text="showLanguage('rego', 'reqInfo')"></p>
                    <div class="text-left">
                        <ul class="requirements">
                            <li>
                                <span v-text="showLanguage('rego', 'reqT1')"></span>
                                <a href="/#/policy" v-text="showLanguage('rego', 'reqT2')"></a>
                                <span v-text="showLanguage('rego', 'reqT3')"></span>
                            </li>
                            <li>
                                <strong v-text="showLanguage('rego', 'reqT4')"></strong>
                                <span v-text="showLanguage('rego', 'reqT5')"></span>
                                    <br>
                            </li>
                            <li>
                                <strong v-text="showLanguage('rego', 'reqT6')"></strong>
                                <span v-text="showLanguage('rego', 'reqT7')"></span>
                            </li>
                            <li>
                                <strong v-text="showLanguage('rego', 'reqT8')"></strong>
                                <span v-text="showLanguage('rego', 'reqT9')"></span>
                            </li>
                            <li>
                                <strong v-text="showLanguage('rego', 'reqT10')"></strong>
                                <span v-text="showLanguage('rego', 'reqT11')"></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="rego-footer">
                    <a class="learn-more-button" href="/#/about" v-text="showLanguage('nav', 'about')" v-on:click="closeThisDialog()"></a>
                    <a class="learn-more-button" href="/#/terms" v-text="showLanguage('nav', 'terms')" v-on:click="closeThisDialog()"></a>
                    <a class="learn-more-button" href="/#/privacy" v-text="showLanguage('nav', 'privacy')" v-on:click="closeThisDialog()"></a></div>
            </div>
        </div>
    </div>
</template>

<script>
    /*
    Imports the event bus.
    */
    import { EventBus } from '../../event-bus.js';

    export default {
        /*
          Defines the data used by the component.
        */
        data() {
            return {
                show: false,
                verifiedName: null,
                verifiedEmail: null,
                verifiedPassword: null,
                credentials: {},
                formStatus: 0
            }
        },

        /*
          Sets up the component on the mounted lifecycle hook.
        */
        mounted() {
            /*
             * When prompted for login, show the component.
             */
            EventBus.$on('prompt-registration', () => {
                this.show = true;

            });
        },

        /*
         *   This modules computed methods
         */
        computed: {
            submitButtonClass() {
                return this.formStatus === 0 ? 'secondary' : 'success';
            },

            submitButtonStatus() {
                return this.formStatus === 0 ? 'true' : 'false';
            },

            enableSubmit() {
                return !(this.formStatus === 1);
            }
        },

        /*
         *   This components active methods
         */
        methods: {
            /*
             * Calls the Translation and Language service
             */
            showLanguage(context, key) {
                return this.$store.getters.getLanguageString( context, key );
            },

            /*
             * Load login modal
             */
            loadLogin() {
                this.show = false;
                EventBus.$emit('prompt-login');
            },

            /*
             *   Validate the name field
             */
            countName() {
                let n = this.credentials.name || false, l = n.length || 0;
                if (!n || l < 5) {
                    this.$jqf(this.$refs.nameInfo).remove('has-success').add('has-error').text('Name length = ' + l +'.  Min 5 characters required');
                    this.$jqf(this.$refs.name).replace(['has-success','has-error']);
                }
                if (l >= 5) {
                    this.$jqf(this.$refs.nameInfo).remove('has-error').add('has-success').text('Name length validated');
                    this.$jqf(this.$refs.name).replace(['has-error','has-success']);
                }
            },

            /*
             *   Verify the name element
             */
            verifyName() {
                let n = this.credentials.name || false;
                if (!n || n.length < 5) {
                    this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Please enter your name first!');
                    this.$jqf(this.$refs.email).replace(['has-success', 'has-error']);
                    this.$jqf(this.$refs.nameInfo).replace(['has-success', 'has-error']).text('Name incomplete!');
                    this.$jqf(this.$refs.name).replace(['has-success', 'has-error']).focus();

                } else {
                    this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Please enter your email address');
                    this.$jqf(this.$refs.email).replace('has-success', 'has-error');
                    this.verifiedName = n;
                }
            },

            /*
             *   Verify the email address
             */
            checkEmail() {
                let e = this.credentials.email || false, self = this;
                if (e.length >= 7) {
                    if (e.indexOf('@') === -1 || e.indexOf('.') === -1) {
                        this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Your email address appears invalid!');
                        this.$jqf(this.$refs.email).replace(['has-success', 'has-error']).focus();

                    } else {
                        if (this.verifiedEmail === e) {
                            this.$refs.password.focus();

                        } else {
                            this.$store.dispatch('checkEmail', { email: e});
                            setTimeout(function() { self.checkEmailResult(e); }, 500);
                        }
                    }
                }
            },

            checkEmailResult(e) {
                let  status = this.$store.getters.getEmailCheckStatus;
                let result = false, self = this;
                if (status === 1) {
                    self.checkEmailResult();
                }
                result = this.$store.getters.getEmailCheck;
                if (status === 2) {
                    if (result.success === true) {
                        if (result.record.email.notFound === 1 && result.record.email.valid === 1) {
                            this.$jqf(this.$refs.emailInfo).replace(['has-error', 'has-success']).text('Email address verified');
                            this.$jqf(this.$refs.email).replace(['has-error', 'has-success']);
                            this.verifiedEmail = e;

                        } else {
                            if (result.record.email.notFound === 0) {
                                this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Your email address was already used - please enter another!');
                            }
                            if (result.record.email.valid === 0) {
                                this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('The email address entered is invalid - please try again!');
                            }
                            this.$jqf(this.$refs.email).replace(['has-success', 'has-error']);
                        }
                    }
                }
                if (status === 3) {
                    this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Unable to verify your email address!');
                }
            },

            verifyEmail() {
                let e = this.credentials.email || false;
                if (e && e !== false) {
                    if (e.indexOf('@') === -1 || e.indexOf('.') === -1 || e !== this.verifiedEmail) {
                        this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Your email address appears invalid!');
                        this.$jqf(this.$refs.email).replace(['has-success', 'has-error']).focus();
                    }

                } else {
                    this.$jqf(this.$refs.emailInfo).replace(['has-success', 'has-error']).text('Please enter your email address first!');
                    this.$jqf(this.$refs.email).replace(['has-success', 'has-error']).focus();

                }
            },

            countPassword() {
                let p = this.credentials.password || undefined;
                if (p !== undefined) {
                    if (p.length < 8) {
                        this.$jqf(this.$refs.passwordInfo).replace(['has-success', 'has-error']).text('Password length = ' + p.length +'.  Min 8 characters required');
                        this.$jqf(this.$refs.password).replace(['has-success', 'has-error']);
                    }
                    if (p.length >= 8) {
                        this.$jqf(this.$refs.passwordInfo).replace(['has-error', 'has-success']).text('Password length validated');
                        this.$jqf(this.$refs.password).replace(['has-error', 'has-success']);
                    }
                }
            },

            verifyPassword() {
                let p = this.credentials.password || false;
                if (p.length < 8) {
                    this.$jqf(this.$refs.passwordConfirmInfo).replace(['has-success', 'has-error']).text('Please provide your password first');
                    this.$jqf(this.$refs.passwordConfirm).replace(['has-success', 'has-error']).focus();
                }
            },

            comparePassword() {
                let p1 = this.credentials.password || false;
                let p2 = this.credentials.matched || undefined;
                if (p2 !== undefined) {
                    if (p2 !== p1) {
                        this.$jqf(this.$refs.passwordConfirmInfo).replace(['has-success', 'has-error']).text('Password entries are not matched');
                        this.$jqf(this.$refs.passwordConfirm).replace(['has-success', 'has-error']);
                    }
                    if (p2 === p1) {
                        this.$jqf(this.$refs.passwordConfirmInfo).replace(['has-error', 'has-success']).text('Passwords match');
                        this.$jqf(this.$refs.passwordConfirm).replace(['has-error', 'has-success']);
                        this.verifiedPassword = p1;
                        this.formStatus = 1;
                    }
                }
            },

            closeThisDialog() {
                this.show = false;
            },

            closeDialogOutside( event ) {
                if ($(event.target).is('#register-modal')) {
                    this.closeThisDialog();
                }
            },

            registerUser( event ) {
                event.preventDefault();
                this.$store.dispatch( 'registerUser', { data: this.credentials } );
            }
        }
    }
</script>
