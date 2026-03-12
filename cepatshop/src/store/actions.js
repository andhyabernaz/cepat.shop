import { Api, BaseApi } from 'boot/axios'
import { getRandomString } from 'src/utils'

export default {
   getAdminConfig({ commit }) {
      commit('SET_LOADING', true)
      BaseApi.get('config').then((response) => {
         if (response.status == 200) {
            this.commit('SET_CONFIG', response.data.data)
         }
      })
         .finally(() => {
            commit('SET_LOADING', false)
         })
   },
   flushData() {
      Api.get('clear-cache')
   },
   getAffiliateConfig({commit}) {
      Api.get('getAffiliateConfig').then(res => {
         commit('SET_AFFILIATE_CONFIG', res.data.data)
      })
   },
   generateSessionId({ commit }) {
      let result = getRandomString()
      commit('SET_SESSION_ID', result);
   },
   getShop: ({ commit }) => {
      Api.get('shop', { silent: true }).then(response => {
         if (response.status == 200) {
            commit('SET_SHOP', response.data.data.shop)
            commit('SET_CONFIG', response.data.data.config)
         }
      })
   },
   getConfig: ({ commit }) => {
      Api.get('config', { silent: true }).then(response => {
         if (response.status == 200) {
            let config = response.data.data
            commit('SET_CONFIG', config)
            commit('cart/SET_SERVICE_FEE', config, { root: true })
         }
      })
   },
   getInitialData: async ({ commit }) => {
      document.body.classList.add('is_loading')
      commit('SET_LOADING', true)
      try {
         let response = await Api.get('getInitialData', { silent: true })
         if (response.status == 200) {
            commit('SET_SHOP', response.data.data.shop)
            commit('SET_CONFIG', response.data.data.config)
            commit('front/SET_INITIAL_DATA', response.data.data, { root: true })
            commit('SET_SESSION_ID', response.data.data.sess_id)
         }
      } finally {
         document.body.classList.remove('is_loading')
         commit('SET_LOADING', false)
      }
   },
   searchAddress({ }, keyword) {
      return Api.get('shipping/searchAddress?q=' + keyword)
   },

   dispatchEvent({ }, event) {
      setTimeout(() => {
         Api.post('dispatchEvent', { event: event })
      }, 2000)
   }
}
