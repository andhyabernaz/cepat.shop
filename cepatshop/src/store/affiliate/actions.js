import { BaseApi } from 'boot/axios'

export function getAffiliate({ commit }) {
   BaseApi.get("affiliate")
      .then(res => {
         commit('SET_AFFILIATE', res.data.data)
      })
}
export function getAllProducts({ commit }, url = null) {
   if(!url) {
      url = "affiliate/products?is_aff=1&per_page=10"
   }
   BaseApi.get(url).then(res => {
      commit('SET_ALL_PRODUCTS', res.data.data)
   });
}
export function getLeads({ commit }) {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get("affiliate/getLeads").then(res => {
      commit('SET_LEADS', res.data.data)
   });
}
export function pageVisited({ commit }, url) {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get(url).then(res => {
      commit('SET_VISITED', res.data.data)
   });
}

