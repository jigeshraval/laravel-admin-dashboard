import Vue from 'vue'
import axios from './plugins/Axios'
import vuetify from './plugins/Vuetify'
import routes from './routes'
import vuex from './store/index.js'

//Default page or layout
import layout from './pages/layout'

const app = new Vue({
    vuex,
    router: routes,
    axios,
    vuetify,
    el: '#dApp',
    render: h => h(layout)
});