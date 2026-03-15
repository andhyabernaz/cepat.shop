import { BaseApi } from 'boot/axios'
export function addBlock({ commit }, payload) {
   commit('SET_LOADING', true, { root: true })
   let formData = new FormData();

   for (const x in payload) {
      if (payload[x]) {
         formData.append(x, payload[x])
      }
   }

   return BaseApi.post('blocks', formData, { headers: { 'Content-Type': 'multipart/formData' } })
}
export function updateBlock({ commit }, payload) {
   commit('SET_LOADING', true, { root: true })
   let formData = new FormData();

   for (const x in payload) {
      if (payload[x]) {
         formData.append(x, payload[x])
      }
   }
   formData.append('_method', 'PUT');

   return BaseApi.post('blocks/' + payload.id, formData, { headers: { 'Content-Type': 'multipart/formData' } })
}

export function getBlocks(context) {
   BaseApi.get('blocks').then(response => {
      if (response.status == 200) {
         context.commit('SET_BLOCKS', response.data.data)
      }
   })
}
export function getAdminBlocks({ commit }) {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get('blocks').then(response => {
      if (response.status == 200) {
         commit('SET_ADMIN_BLOCKS', response.data.data)
      }
   })
}
export function getBlockById(context, id) {
   return BaseApi.get('blocks/' + id)
}
export function deleteBlock({ commit, dispatch }, id) {
   commit('SET_LOADING', true, { root: true })
   commit('REMOVE_BLOCKS', id)
   BaseApi.delete('blocks/' + id).then(res => {
      dispatch('getAdminBlocks')
   })
}

export function setPostLink(context, payload) {
   return BaseApi.post('blocks/setPostLink', payload)
}
