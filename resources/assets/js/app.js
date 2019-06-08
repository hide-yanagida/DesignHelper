
// /**
//  * First we will load all of this project's JavaScript dependencies which
//  * includes Vue and other libraries. It is a great starting point when
//  * building robust, powerful web applications using Vue and Laravel.
//  */
//
// require('./bootstrap');
//
// window.Vue = require('vue');
//
// /**
//  * Next, we will create a fresh Vue application instance and attach it to
//  * the page. Then, you may begin adding components to this application
//  * or customize the JavaScript scaffolding to fit your unique needs.
//  */
//
// Vue.component('example-component', require('./components/ExampleComponent.vue'));
//
// const app = new Vue({
//     el: '#app'
// });
require('./bootstrap')


import Vue from 'vue'
import store from './store';
import router from './router';

//import { MdApp, MdToolbar, MdDrawer, MdContent, MdList, MdIcon } from 'vue-material/dist/components'
import VueMaterial from 'vue-material'
import 'vue-material/dist/vue-material.min.css'
import 'vue-material/dist/theme/default.css'
Vue.use(VueMaterial)

import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

import localStorage from './localStorage';
Vue.mixin(localStorage);

import Toasted from 'vue-toasted';
Vue.use(Toasted)
let options = {
    type : 'error',
    icon : 'error_outline',
    duration : 5000
};

// register the toast with the custom message
Vue.toasted.register('error',
    (payload) => {

        // if there is no message passed show default message
        if(! payload.message) {
            return "Oops.. Something Went Wrong.."
        }

        // if there is a message show it with the message
        return "Oops.. " + payload.message;
    },
    options
)



//import BootstrapVue from 'bootstrap-vue'
//Vue.use(BootstrapVue)

window.state = store.state;

Vue.component('app', require('./components/App.vue'));

const app = new Vue({
    router
}).$mount('#app');

// import 'bootstrap/dist/css/bootstrap.css'
// import 'bootstrap-vue/dist/bootstrap-vue.css'
//window.Vue = Vue;


// import VueRouter from 'vue-router'
//
// //require('./bootstrap')
//
// Vue.use(VueRouter)
//
// // Vue.component('navbar', require('./components/Layouts/Navbar.vue'))
// // Vue.component('admin-side-menu', require('./components/Admin/SideMenu'))
// // Vue.component('regist-user', require('./components/Admin/RegistUser'))
//
// const router = new VueRouter({
//     mode: 'history',
//     routes: [
//         // { path: '/', component: require('./components/Articles/Index.vue') },
//         // { path: '/about', component: require('./components/About.vue') },
//         // { path: '/bstest', component: require('./components/Bstest.vue') },
//         // { path: '/admin', component: require('./components/Admin/Index.vue') }
//     ]
// })
//
// const app = new Vue({
//     router,
//     el: '#app'
// })