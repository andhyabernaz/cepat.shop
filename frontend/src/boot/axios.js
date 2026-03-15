import { boot } from 'quasar/wrappers'
import axios from 'axios'

import { Loading, Notify } from 'quasar'

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)

var BaseApi = axios.create({
   baseURL: process.env.API
})

var Api = axios.create({
   baseURL: process.env.PUBLIC_API
})

Api.defaults.headers.common = { 'X-Requested-With': 'XMLHttpRequest' }
BaseApi.defaults.headers.common = { 'X-Requested-With': 'XMLHttpRequest' }

export default boot(({ app, router, store, urlPath }) => {
   store.$router = router
   // for use inside Vue files (Options API) through this.$axios and this.$api

   app.config.globalProperties.$axios = axios
   // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
   //       so you won't necessarily have to import axios in each vue file

   app.config.globalProperties.$api = BaseApi
   // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
   //       so you can easily perform requests against your app's API

   Api.interceptors.request.use(config => {

      let session_id = store.state.session_id
      if (session_id) {
         config.headers['Session-User'] = session_id;
      }
      let token = store.state.user.token
      if (token) {
         config.headers['Authorization'] = `Bearer ${token}`
      }
      return config
   }, error => {
      Notify.create({
         type: 'negative',
         message: 'Network Error'
      })
      return Promise.reject(error)
   })
   BaseApi.interceptors.request.use(config => {

      let session_id = store.state.session_id
      if (session_id) {
         config.headers['Session-User'] = session_id;
      }
      let token = store.state.user.token
      if (token) {
         config.headers['Authorization'] = `Bearer ${token}`
      }
      return config
   }, error => {
      // Do something with request error
      Loading.hide()
      store.commit('SET_LOADING', false)

      Notify.create({
         type: 'negative',
         message: 'Network Error'
      })
      return Promise.reject(error)
   })

   // Add a response interceptor
   BaseApi.interceptors.response.use(response => {
      store.commit('SET_LOADING', false)
      Loading.hide()
      if (response.data.event) {
         store.dispatch('dispatchEvent', response.data.event)
      }
      return response

   }, error => {
      store.commit('SET_LOADING', false)
      Loading.hide()

      // var errors = error
      var errors = []
      let showMessage = true

      if (error?.config?.silent === true) {
         return Promise.reject(error)
      }

      if (error.response) {

         if (error.response.status == 503) {

            if (error.response.data.success == false && error.response.data.is_installed == false) {
               store.commit('CAN_INSTALL', true)

               if (!urlPath.includes('install')) {
                  router.push({ name: 'InstallApp' })
               }

            }
         }

         // Session Expired
         if (401 === error.response.status) {
            showMessage = false
            errors.push(error.response.data.message)
            store.dispatch('user/exit')
            Notify.create({
               type: 'negative',
               message: 'Sesi anda sudah habis, silahkan login'
            });
         }

         // Errors from backend
         if (error.response.status == 422) {
            // errors = error.response.data.message

            for (var errorKey in error.response.data.errors) {
               for (var i = 0; i < error.response.data.errors[errorKey].length; i++) {
                  errors.push(String(error.response.data.errors[errorKey][i]))
               }
            }
         }

         // Backend error
         if (500 === error.response.status) {
            errors.push('Terjadi gangguan pada server, silahkan coba lagi.')
         }
         if (400 === error.response.status) {
            errors.push(error.response.data.message)
            // errors.push('Jaringan sedang bermasalah, silahkan tunggu beberapa saat.')
         }

      } else {
         errors.push('Jaringan sedang bermasalah, silahkan tunggu beberapa saat.')
      }

      if (showMessage && errors.length) {
         Notify.create({
            type: 'negative',
            message: String(errors[0]),
         })
      }

      // Do something with response error       
      return Promise.reject(error)
   })

   Api.interceptors.response.use(response => {
      store.commit('SET_LOADING', false)
      Loading.hide()
      if (response.data.event) {
         store.dispatch('dispatchEvent', response.data.event)
      }
      return response

   }, error => {
      store.commit('SET_LOADING', false)
      Loading.hide()
      var errors = []

      if (error?.config?.silent === true) {
         return Promise.reject(error)
      }

      if (error.response.status == 503) {

         if (error.response.data.success == false && error.response.data.is_installed == false) {
            store.commit('CAN_INSTALL', true)

            if (!urlPath.includes('install')) {
               router.push({ name: 'InstallApp' })
            }

         }
      }

      // Session Expired
      if (401 === error.response.status) {
         errors.push('something when wrong!, please contact adminisrator')
         store.dispatch('user/exit')
      }

      // Errors from backend
      if (error.response.status == 422) {
         // errors = error.response.data.message

         for (var errorKey in error.response.data.errors) {
            for (var i = 0; i < error.response.data.errors[errorKey].length; i++) {
               errors.push(String(error.response.data.errors[errorKey][i]))
            }
         }
      }

      if (errors.length) {
         Notify.create({
            type: 'negative',
            message: errors[0],
         })
      }

      return Promise.reject(error)
   })
})

export { BaseApi, Api }
