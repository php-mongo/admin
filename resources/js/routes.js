/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      routes.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      routes.js
 * @subpackage   Id
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See COPYRIGHT.php for copyright notices and further details.
 */

/*
*   routes.js
*
*   Contains all routes for this application
*/

/*
*    Import Vue and VueRouter to extend the routes
*/
import Vue from 'vue';
import VueRouter from 'vue-router';
import store from './store.js'

/*
*   Extends Vue to use VueRouter
*/
Vue.use( VueRouter );

/*
*   Create a VueRouter that is used to handle all the application routes
*/
let router = new VueRouter({
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0}
    },
    routes: [
        {
            path: '/',
            name: 'layout',
            redirect: { name: 'admin' },
            component: Vue.component( 'Layout', require( './layouts/Layout.vue' ).default ),
            meta: {
              requiresAuth: true
            },
            children: [
                {
                    path: 'admin',
                    name: 'admin',
                    component: Vue.component( 'Admin', require( './pages/Admin.vue').default )
                },
                {
                    path: 'about',
                    name: 'about',
                    component: Vue.component( 'About', require( './pages/About.vue').default )
                },
                {
                    path: 'about-us',
                    name: 'about-us',
                    component: Vue.component( 'About', require( './pages/About.vue').default )
                },
                /*{
                    path: 'post',
                    name: 'post',
                    component: Vue.component( 'Post', require( './pages/Post.vue').default )
                },*/
                {
                    path: 'terms',
                    name: 'terms',
                    component: Vue.component( 'Terms', require( './pages/Terms.vue').default )
                },
                {
                    path: 'terms-and-conditions',
                    name: 'terms-and-conditions',
                    component: Vue.component( 'Terms', require( './pages/Terms.vue').default )
                },
            ]
        },
        {
            path: '/public',
            name: 'public',
            redirect: {name: 'login' },
            component: Vue.component('Public', require('./layouts/Public.vue').default ),
            children: [
                {
                    path: 'login',
                    name: 'login',
                    component: Vue.component( 'Login', require( './pages/Login.vue').default )
                },
                {
                    path: 'contact',
                    name: 'contact',
                    component: Vue.component( 'Contact', require( './pages/Contact.vue').default )
                },
                {
                    path: 'privacy',
                    name: 'privacy',
                    component: Vue.component( 'Privacy', require( './pages/Privacy.vue').default )
                },
                {
                    path: 'privacy-policy',
                    name: 'privacy-policy',
                    component: Vue.component( 'Privacy', require( './pages/Privacy.vue').default )
                }
            ],
        }
    ]
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.isLoggedIn) {
            next();
            return;
        }
        next('/public/login');

    } else {
        next();
    }
});

export default router;
