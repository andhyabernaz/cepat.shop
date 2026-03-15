import { BaseApi, Api } from 'boot/axios'

export function productStore({ dispatch, commit }, payload) {

   commit('SET_LOADING', true, { root: true })
   return BaseApi.post('products', payload)
      .then(() => {
         dispatch('getAdminProducts')
         this.$router.push({ name: 'AdminProductIndex' })

      })

}
export function productUpdate({ commit, dispatch }, payload) {
   payload._method = 'PUT'
   commit('SET_LOADING', true, { root: true })
   BaseApi.post('products/' + payload.id, payload)
      .then(() => {
         dispatch('getAdminProducts')
         this.$router.push({ name: 'AdminProductIndex' })
      })
}

export function getAdminProducts({ commit }, url = 'products') {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get(url).then(response => {
      commit('SET_ADMIN_PRODUCTS', response.data.data)
   })
}

export function getProductById({ }, id) {
   return BaseApi.get('products/' + id)
}

export function productDelete({ dispatch, commit }, id) {
   commit('SET_LOADING', true, { root: true })
  return BaseApi.delete('products/' + id)
}

export function removeVarian({ }, id) {
   return BaseApi.delete(`products/${id}/removeVarian`)
}

// FRONT
export function getProducts({ commit }, query = null) {
   let url = 'getProducts'
   if (query) {
      url += `?${new URLSearchParams(query).toString()}`
   }
   Api.get(url).then(response => {
      commit('SET_PRODUCTS', response.data)
   })
}

export function productDetail({ }, slug) {
   return Api.get('product-item/' + slug)
}

export function searchProducts({ commit }, q) {
   return Api.get('getProducts?search=' + q)
}

export function productsByCategory({ commit }, query) {
   commit('CLEAR_PRODUCT_CATEGORY')
   let url = `getProducts`

   if (Object.entries(query).length) {
      url += `?${new URLSearchParams(query).toString()}`
   }

   Api.get(url).then(response => {
      if (response.status == 200) {
         commit('SET_PRODUCT_CATEGORY', response.data)
      }
   })
}

export function getProductsFavorites({ commit }, payload) {
   Api.post('product-favorites', payload).then(response => {
      if (response.status == 200) {
         commit('SET_PRODUCT_FAVORITE', response.data)
      }
   })
}
export function addProductReview({ commit }, payload) {
   return Api.post('product-review', payload)
}
export function loadProductReview({ }, payload) {
   if (payload.skip) {
      return Api.get('product-review/' + payload.product_id + '?skip=' + payload.skip)
   } else {
      return Api.get('product-review/' + payload.product_id)
   }
}

