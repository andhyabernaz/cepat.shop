export function SET_PRODUCT(state, payload) {
   state.products.data.push(payload)
}

export function SET_PRODUCTS(state, payload) {
   state.products.data = payload.results
   state.products.links = payload.links
   state.products.meta = payload.meta
   state.products.ready = true
   state.products.available = payload.results.length > 0 ? true : false

}
export function PAGINATE_PRODUCTS(state, payload) {
   state.products.data = [...state.products.data, ...payload.results]
   state.products.links = payload.links
   state.products.meta = payload.meta
}

export function SET_PRODUCT_PROMO(state, payload) {
   state.product_promo = payload

}
export function SET_ADMIN_PRODUCTS(state, payload) {
   state.admin_products = { ...state.products, ...payload }
   state.admin_products.ready = true
}
export function PAGINATE_ADMIN_PRODUCTS(state, payload) {
   state.admin_products.data = [...state.admin_products.data, ...payload.data]
   state.admin_products.links = payload.links
   state.admin_products.total = payload.total
   state.admin_products.next_page_url = payload.next_page_url
   state.admin_products.last_page_url = payload.last_page_url
   state.admin_products.ready = true
   state.admin_products.available = true
}
export function UPDATE_PRODUCT(state, payload) {
   let productIndex = state.admin_products.data.findIndex(e => e.id == payload.id)

   state.admin_products.data[productIndex] = payload

}

export function SET_INITIAL_PRODUCTS(state, payload) {
   state.initial_products.data = payload
   state.initial_products.ready = true
   state.initial_products.available = payload.length > 0 ? true : false
}


export function SET_PRODUCT_SEARCH(state, payload) {
   state.productSearch.data = payload
   state.productSearch.ready = true
   state.productSearch.available = payload.length > 0 ? true : false
}
export function CLEAR_PRODUCT_SEARCH(state) {
   state.productSearch.data = []
   state.productSearch.ready = false
   state.productSearch.available = true
}
export function SET_SEARCH_KEY(state, payload) {
   state.searchKey = payload
}
export function ADD_REMOVE_TO_FAVORITE(state, fid) {
   state.productFavorites.data = state.productFavorites.data.filter(el => el.id != fid)

   if (state.productFavorites.data.length == 0) {
      state.productFavorites.available = false
      state.productFavorites.ready = true
   }

   if (state.favorites.length > 0) {
      let n = state.favorites.find(e => e == fid)
      if (n != undefined) {
         state.favorites = state.favorites.filter(h => h != fid)
      } else {
         state.favorites.push(fid)
      }
   } else {
      state.favorites.push(fid)
   }
   // localStorage.setItem('wacommerce-favorites', JSON.stringify(state.favorites))
}
export function SET_FRONT_RODUCT_READY(state, status) {
   state.terbaru.ready = status
   state.terbaru.terlaris = status
}

export function SET_PRODUCT_CATEGORY(state, payload) {
   state.productsByCategory = { ...payload }
   state.productsByCategory.ready = true
   state.productsByCategory.available = state.productsByCategory.data.length > 0 ? true : false

}

export function PAGINATE_PRODUCT_CATEGORY(state, payload) {
   state.productsByCategory.data = [...state.productsByCategory.data, ...payload.data]
   state.productsByCategory.links = payload.links
   state.productsByCategory.meta = payload.meta

}
export function CLEAR_PRODUCT_CATEGORY(state) {
   state.productsByCategory.data = []
   state.productsByCategory.ready = false
   state.productsByCategory.available = true
}
export function SET_PRODUCT_FAVORITE(state, payload) {

   state.productFavorites = { ...payload }
   state.productFavorites.ready = true
   state.productFavorites.available = state.productFavorites.data.length > 0 ? true : false

   if (!state.productFavorites.data.length) {
      state.favorites = []
   }
   if (state.productFavorites.data.lenght < state.favorites.length) {
      state.favorites = []
      state.productFavorites.data.forEach(el => {
         state.favorites.push(el.id)
      });
   }
}
export function CLEAR_PRODUCT_FAVORITE(state) {
   state.productFavorites.data = []
   state.productFavorites.ready = false
   state.productFavorites.available = true
}
export function SET_PRODUCT_FAVORITE_STATUS(state, payload) {
   state.productFavorites.ready = payload.ready
   state.productFavorites.available = payload.available
}
export function UPDATE_FIELD_PRODUCT(state, payload) {
   state.form[payload.key] = payload.value
}
export function SET_CATEGORY_PRODUCTS(state, payload) {
   state.category_products = payload.map(cat => {
      return { ...cat, product_items: [] }
   })

}
