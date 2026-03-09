import { Api } from 'boot/axios'

export function getCarts({ commit, rootState }) {
   Api.get('carts').then(response => {
      if (response.status == 200) {
         commit('SET_CARTS', response.data.data)

         const config = rootState.config

         if (config && config.is_service_fee) {
            commit('SET_SERVICE_FEE', config)
         }

      }
   })

}
export function addToCart({ commit, dispatch }, payload) {

   commit('ADD_TO_CART', payload)

   Api.post('carts', payload)
      .finally(() => {
         dispatch('getCarts')
      })

}
export function addToCartDeposit({ commit }, payload) {

   commit('CLEAR_CART', payload)

   return Api.post('carts', payload)
}
export function updateCart({ commit, dispatch }, payload) {

   commit('UPDATE_CART', payload)

   Api.post('carts/' + payload.sku, { ...payload, _method: 'PUT' })
      .finally(() => {
         dispatch('getCarts')
      })

}

export function removeCart({ commit, dispatch }, payload) {

   commit('REMOVE_CART', payload)
   Api.delete('carts/' + payload.sku)

      .finally(() => {
         dispatch('getCarts')
      })

}

export function clearCart({ commit, dispatch }) {
   commit('CLEAR_CART')
   Api.post('carts/clear')
      .finally(() => {
         dispatch('getCarts')
      })
}
