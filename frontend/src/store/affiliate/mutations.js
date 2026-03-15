
export function SET_AFFILIATE(state, payload) {
   state.affiliate = payload
}
export function SET_ALL_PRODUCTS(state, payload) {
   state.all_products = {...payload}
   state.all_products.available = state.all_products.total > 0 ? true : false
}
export function SET_PRODUCTS(state, payload) {
   state.products.data = payload
   state.products.available = payload.length ? true : false
}
export function SET_LEADS(state, payload) {
   state.leads.data = payload.data
   state.leads.links = payload.links
   state.leads.available = payload.total > 0 ? true : false
}
export function SET_VISITED(state, payload) {
   state.page_visited = {...payload}
}

