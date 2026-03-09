import { BaseApi } from 'boot/axios'

export function getPromos({ commit }, status = 'active') {
   BaseApi.get(`promos?status=${status}`).then(response => {
      if (response.status == 200) {
         commit('SET_PROMOS', response.data.data)
      }
   })
}
export function storePromo({ }, payload) {
   return BaseApi.post('promos', payload)
}
export function updatePromo({ }, payload) {
   return BaseApi.post('promos/' + payload.id, payload)
}
export function deletePromo({ }, id) {
   return BaseApi.delete('promos/' + id)
}
export function getProductPromo({ commit }, id) {
   BaseApi.get('promo/products/' + id).then((response) => {
      if (response.status == 200) {
         commit('SET_PRODUCT_PROMO', response.data.data)
      }
   })
}




