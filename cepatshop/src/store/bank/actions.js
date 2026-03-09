import { BaseApi } from 'boot/axios'

export function getBanks({ commit }) {
   BaseApi.get('banks').then(response => {
      if (200 == response.status) {
         commit('SET_BANKS', response.data.data)
      }
   })
}
export function storeBank({ dispatch }, payload) {
   BaseApi.post('banks', payload).then(response => {
      if (200 == response.status) {
         dispatch('getBanks')
      }
   })
}
export function updateBank({ dispatch }, payload) {
   BaseApi.post('banks/' + payload.id, payload).then(response => {
      if (200 == response.status) {
         dispatch('getBanks')
      }
   })
}
export function destroyBank({ dispatch }, id) {
   BaseApi.delete('banks/' + id).then(response => {
      if (200 == response.status) {
         dispatch('getBanks')
      }
   })
}


