import { BaseApi } from 'boot/axios'

export function addPost({ commit, dispatch }, payload) {
   let formData = new FormData();
   for (const x in payload) {
      if (payload[x] && payload[x] != '') {
         formData.append(x, payload[x])
      }
   }
   let self = this
   commit('SET_LOADING', true, { root: true })
   BaseApi.post('posts', formData, { headers: { 'content-Type': 'multipart/formData' } }).then(response => {
      if (response.status == 200) {
         dispatch('getAllPost')
         self.$router.push({ name: 'AdminPostIndex' })
         commit('SET_LOADING', false, { root: true })
      }
   }).finally(() => {
      commit('SET_LOADING', false, { root: true })
   })
}
export function updatePost({ commit, dispatch }, payload) {
   commit('SET_LOADING', true, { root: true })
   let self = this
   let formData = new FormData();
   for (const x in payload) {
      if (payload[x] && payload[x] != '') {
         formData.append(x, payload[x])
      }
   }
   formData.append('_method', 'PUT')

   BaseApi.post('posts/' + payload.id, formData, { headers: { 'content-Type': 'multipart/formData' } }).then(response => {
      if (response.status == 200) {
         self.$router.push({ name: 'AdminPostIndex' })
         dispatch('getAllPost')
      }
   }).finally(() => {
      commit('SET_LOADING', false, { root: true })
   })
}
export function deletePost({ commit, dispatch }, id) {
   commit('SET_LOADING', false, { root: true })
   BaseApi.delete('posts/' + id).then(response => {
      if (response.status == 200) {
         dispatch('getAllPost')
      }
   }).finally(() => {
      commit('SET_LOADING', false, { root: true })
   })
}
export function getAllPost({ commit }, url = 'posts') {
   BaseApi.get(url).then(response => {
      if (response.status == 200) {
         commit('SET_ALL_POST', response.data.data)
      }
   })
}

export function getSinglePost(context, id) {
   return BaseApi.get('posts/' + id)
}

