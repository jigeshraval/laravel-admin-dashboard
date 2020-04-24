import Vue from 'vue'
import axios from 'axios'
import store from '../store/index.js'
var ax = axios;
var axiosx = axios.create({
   baseURL: 'http://127.0.0.1:8000/' + ADMIN_API_ROUTE
})
Vue.prototype.$axiosx = axiosx;
Vue.prototype.$axios = ax;
axiosx.interceptors.request.use(function (config) {
   store.commit('loading', true);
   // const token = cookie.get(__TOKEN_KEY__);
   // if (token != null) {
   //     config.headers.Authorization = Bearer ${token};
   // }
   return config;
}, function (err) {
   return Promise.reject(err);
});
ax.interceptors.request.use(function (config) {
   store.commit('loading', true);
   // const token = cookie.get(__TOKEN_KEY__);
   // if (token != null) {
   //     config.headers.Authorization = Bearer ${token};
   // }
   return config;
}, function (err) {
   return Promise.reject(err);
});
axiosx.interceptors.response.use(function (res) {

   console.log(res);

   store.commit('loading', false);

   var snacktext = null;

   if (res.data && res.data.snackText) {
       var snackText = res.data.snackText
   }

   if (snackText) {
       store.commit('snackbar', { status: true, text: snackText });
   }

   // Do something with response data
   return res;
}, function (error) {

      store.commit('loading', false);

      var res = error.response;
      var errors = res.data.errors.name;

      if (errors && errors[0]) {

         store.commit('snackbar', {
            status: true,
            text: errors[0]
         });

      }
});
ax.interceptors.response.use(function (response) {

   console.log(response);
   store.commit('loading', false);
   // Do something with response data
   return response;

}, function (response) {
   
   store.commit('loading', false);
   var res = error.response;
   console.log(res);

});
export default ax;
