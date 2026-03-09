import { BaseApi } from 'boot/axios'
import { Notify } from 'quasar'

export function getAllCategories({ commit }) {
   BaseApi.get('categories').then(response => {
      if (response.status == 200) {
         commit('SET_ALL_CATEGORIES', response.data.data);
      }
   })
}
export function getCategoriesWithChilds({ commit }) {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get('categories?with=childs').then(response => {
      if (response.status == 200) {
         commit('SET_CATEGORY_WITH_CHILDS', response.data.data);
         commit('front/CLEAR_FRONT_CATEGORIES', null, { root: true });
      }
   })
}
export function getCategory({ }, payload) {
   return BaseApi.get('categories/' + payload)
}
export function categoryStore({ dispatch, commit }, payload) {
   let self = this
   commit('SET_LOADING', true, { root: true })
   return BaseApi.post('categories', payload, { headers: { 'content-Type': 'multipart/formData' } })
      .then(response => {
         if (response.status == 200) {
            dispatch('getCategoriesWithChilds')
            Notify.create({
               type: 'positive',
               message: 'Berhasil menambah data'
            })
            self.$router.push({ name: 'CategoryIndex' })
         }
      })
}
export function categoryUpdate({ dispatch, commit }, payload) {
   let self = this
   commit('SET_LOADING', true, { root: true })
   // console.log(Object.fromEntries(payload));
   BaseApi.post('categories/' + payload.id, payload.data, { headers: { 'content-Type': 'multipart/formData' } })
      .then(response => {
         if (response.status == 200) {
            dispatch('getCategoriesWithChilds');
            Notify.create({
               type: 'positive',
               message: 'Berhasil memperbarui data'
            })
            self.$router.push({ name: 'CategoryIndex' })
         }
      })
}
export function categoryDelete({ commit, dispatch }, payload) {
   commit('SET_LOADING', true, { root: true })
   BaseApi.delete('categories/' + payload)
      .then(response => {
         commit('SET_LOADING', false, { root: true })
         dispatch('getCategoriesWithChilds')
         Notify.create({
            type: 'positive',
            message: 'Berhasil menghapus data'
         })
      })

}
export function getCategories({ commit }) {
   BaseApi.get('categories').then(response => {
      if (response.status == 200) {
         commit('SET_CATEGORIES', response.data.data);
      }
   })
}