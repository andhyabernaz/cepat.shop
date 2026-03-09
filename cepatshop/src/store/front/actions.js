import { Api } from 'boot/axios'

export function getPromotePosts({ commit }) {
   Api.get('getPromotePosts').then(res => {
      commit('SET_PROMOTE_POSTS', res.data.data)
   })
}
export function getCategories({ }, params = {}) {

   let url = 'getCategories'

   if (Object.entries(params).length) {
      url += `?${new URLSearchParams(params).toString()}`
   }

   return Api.get(url)
}
export function getSliders({ commit }) {
   Api.get('getSliders').then(res => {
      if (res.status == 200) {
         commit('SET_SLIDERS', res.data.data)
      }
   })
}
export function getPosts({ commit }, url) {
   Api.get(url).then(res => {
      commit('SET_POSTS', res.data.data)
   })
}
export function getProducts({ commit }, params = {}) {
     let url = 'getProducts'

   if (Object.entries(params).length) {
      url += `?${new URLSearchParams(params).toString()}`
   }
   Api.get(url).then(res => {
      commit('SET_PRODUCT_LIST', res.data)
   })
}

export function getAllCategories({ commit }) {
   let url = 'getCategories'
   return Api.get(url).then(res => {
      commit('SET_ALL_CATEGORIES', res.data.data)
   })
}

export function getInvoice({ }, order_ref) {
   return Api.get('invoice/' + order_ref)
}
export function storeOrder({ }, payload) {
   return Api.post('storeorder', payload)
}
export function shippingWaybill({ }, waybill) {
   return Api.get('shipping/tracking/' + waybill)
}
