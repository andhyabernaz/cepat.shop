import { BaseApi } from 'boot/axios'
import { Notify } from 'quasar'

export function getSliders({ commit }) {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get('sliders').then(response => {
      commit('SET_SLIDERS', response.data.data)
   })
}

export function removeSlider({ commit, dispatch }, id) {
   commit('SET_LOADING', true, { root: true })
   commit('SET_DATA_STATUS', false)
   BaseApi.delete('sliders/' + id).then(() => {
      dispatch('getSliders')
      commit('REMOVE_SLIDER', id)
      Notify.create({
         type: 'positive',
         message: 'Berhasil menghapus data'
      })
   }).catch(() => {
      commit('SET_DATA_STATUS', true)
   })
}

export function updateSliderWeight({ commit, dispatch }, payload) {
   commit('SET_DATA_STATUS', false)
   BaseApi.post('slider/update-weight', payload).then(() => {
      dispatch('getSliders')
   }).catch(() => {
      commit('SET_DATA_STATUS', true)
   })
}
export function setPostLink({ commit }, payload) {
   commit('SET_LOADING', true, { root: true })
   return BaseApi.post('slider/setPostLink', payload)
}