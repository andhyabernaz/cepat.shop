import { BaseApi } from 'boot/axios'

export function getPromotes({ commit }) {
  BaseApi.get('promote').then(response => {
    if (response.status == 200) {
      commit('SET_PROMOTES', response.data.data)
    }
  })
}
export function storePromote({ dispatch }, payload) {
  BaseApi.post('promote', payload).then(() => {
    dispatch('getPromotes')
  })
}
export function updatePromote({ dispatch }, payload) {
  BaseApi.post('promote/' + payload.id, payload).then(() => {
    dispatch('getPromotes')
  })
}
export function deletePromote({ dispatch }, id) {
  BaseApi.delete('promote/' + id).then(() => {
    dispatch('getPromotes')
  })
}
export function getProducts({ commit }, id) {
  BaseApi.get('getProductPromo' + id).then((response) => {
    if (response.statu == 200) {
      commit('SET_PRODUCT_PROMO', response.data.data)
    }
  })
}




