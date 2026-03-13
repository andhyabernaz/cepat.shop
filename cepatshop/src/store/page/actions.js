import { BaseApi } from 'boot/axios'

const buildFormData = (payload) => {
   const formData = new FormData()

   const keys = [
      'title',
      'slug',
      'category',
      'status',
      'theme',
      'meta_description',
      'content',
      'pixel_id',
      'pixel_access_token',
      'pixel_test_event_code',
      'remove_featured_image',
   ]

   keys.forEach((key) => {
      if (payload[key] === undefined || payload[key] === null) return
      formData.append(key, payload[key])
   })

   if (payload.featured_image) {
      formData.append('featured_image', payload.featured_image)
   }

   return formData
}

export function addPage({ commit, dispatch }, payload) {
   const formData = buildFormData(payload)
   let self = this
   commit('SET_LOADING', true, { root: true })
   BaseApi.post('pages', formData, { headers: { 'content-Type': 'multipart/formData' } })
      .then((response) => {
         if (response.status == 200) {
            dispatch('getAllPage')
            self.$router.push({ name: 'AdminPageIndex' })
            commit('SET_LOADING', false, { root: true })
         }
      })
      .finally(() => {
         commit('SET_LOADING', false, { root: true })
      })
}

export function updatePage({ commit, dispatch }, payload) {
   commit('SET_LOADING', true, { root: true })
   let self = this
   const formData = buildFormData(payload)
   formData.append('_method', 'PUT')

   BaseApi.post('pages/' + payload.id, formData, { headers: { 'content-Type': 'multipart/formData' } })
      .then((response) => {
         if (response.status == 200) {
            self.$router.push({ name: 'AdminPageIndex' })
            dispatch('getAllPage')
         }
      })
      .finally(() => {
         commit('SET_LOADING', false, { root: true })
      })
}

export function deletePage({ commit, dispatch }, id) {
   commit('SET_LOADING', false, { root: true })
   BaseApi.delete('pages/' + id)
      .then((response) => {
         if (response.status == 200) {
            dispatch('getAllPage')
         }
      })
      .finally(() => {
         commit('SET_LOADING', false, { root: true })
      })
}

export function getAllPage({ commit }, url = 'pages') {
   BaseApi.get(url).then((response) => {
      if (response.status == 200) {
         commit('SET_ALL_PAGE', response.data.data)
      }
   })
}

export function getSinglePage(context, id) {
   return BaseApi.get('pages/' + id)
}

