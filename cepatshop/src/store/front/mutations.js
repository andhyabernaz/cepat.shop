
export function SET_INITIAL_DATA(state, payload) {
   // SET SLIDER
   state.sliders.data = payload.sliders
   state.sliders.ready = true
   state.sliders.available = state.sliders.data > 0 ? true : false

   // SET BLOCK

   if (payload.blocks.length) {
      state.blocks.banner = payload.blocks.filter(el => el.position == 'Top');
      state.blocks.featured = payload.blocks.filter(el => el.position == 'Bottom');
   }
   state.blocks.ready = true;
   state.blocks.available = payload.blocks.length ? true : false;

   // SET PRODUCT PROMO

   state.product_promo = payload.product_promo

   state.promote_posts.count = payload.post_promote_count

   if (payload.categories.length != (state.categories.data ? state.categories.data.length : 0)) {
      state.categories.data = payload.categories
      state.categories.ready = true
      state.categories.available = state.categories.data.length > 0

      state.products = payload.categories.map(cat => ({ category_id: cat.id, category_slug: cat.slug, data: [], is_done: false, is_available: true }))
   }

   state.is_loaded = true
}

export function SET_BLOCKS(state, payload) {
   if (payload.length) {
      state.blocks.banner = payload.filter(el => el.position == 'Top');
      state.blocks.featured = payload.filter(el => el.position == 'Bottom');
   }
   state.blocks.ready = true;
   state.blocks.available = payload.length ? true : false;
}

export function SET_PRODUCT_PROMO(state, payload) {
   state.product_promo = payload

}
export function SET_POST_TAGS(state, payload) {
   state.post_tags = payload

}
export function SET_CATEGORIES(state, payload) {

   if (payload.length != (state.categories.data ? state.categories.data.length : 0)) {
      state.categories.data = payload
      state.categories.ready = true
      state.categories.available = state.categories.data.length > 0

      state.products = payload.map(cat => ({ category_id: cat.id, data: [], is_done: false, is_available: true }))
   }

}
export function CLEAR_FRONT_CATEGORIES(state) {

   state.categories.data = []
   state.categories.ready = false
   state.categories.available = true

}
export function SET_CATEGORY_MENU(state, data) {

   state.category_menu = data

}
export function SET_PRODUCT_CATEGORY(state, payload) {

   let idx = state.products.findIndex(el => el.category_id == payload.category_id)

   if (idx >= 0) {
      state.products[idx].data = payload.product_items
      state.products[idx].is_done = true
   }

}

export function SET_SLIDERS(state, payload) {
   state.sliders.data = payload
   state.sliders.ready = true
   state.sliders.available = payload.length > 0 ? true : false
}

export function SET_SLIDERS_COUNT(state, total) {
   state.sliders.count = total
}

export function SET_LOADED(state) {
   state.is_loaded = true
}
export function REMOVE_PROMO(state, id) {
   state.product_promo = state.product_promo.filter(el => el.id != id)
}
export function SET_POSTS(state, data) {
   state.posts = { ...data }
   state.posts.ready = true
   state.posts.available = state.posts.data.length > 0
}
export function SET_PROMOTE_POSTS(state, data) {
   state.promote_posts.data = data
   state.promote_posts.ready = true
}
export function SET_PRODUCT_LIST(state, payload) {
   state.product_list = { ...payload }
   state.product_list.ready = true
   state.product_list.available = payload.data.length > 0 ? true : false
}

export function PAGINATE_PRODUCTS(state, payload) {
   state.product_list.data = [...state.product_list.data, ...payload.data]
   state.product_list.links = payload.links
   state.product_list.meta = payload.meta
}
export function SET_ALL_CATEGORIES(state, payload) {
   state.all_categories = payload
}