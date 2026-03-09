
export function SET_CATEGORIES ( state, payload ) {
  if(payload.length) {
    state.categories.data = payload
  }
  state.categories.ready = true
  state.categories.available = payload.length > 0 ? true : false
}

export function SET_CATEGORY_WITH_CHILDS ( state, payload ) {
  if(payload.length) {
    state.category_with_childs.data = payload
  }
  state.category_with_childs.ready = true
  state.category_with_childs.available = payload.length > 0 ? true : false
}

export function SET_ALL_CATEGORIES ( state, payload ) {
  state.all_categories.data = payload
  state.all_categories.ready = true
  state.all_categories.available = payload.length > 0 ? true : false
}
export function UPDATE_CATEGORIES ( state, payload ) {
  state.categories = state.categories.filter(el => el.id != payload)
}
export function SET_CATEGORY( state, payload ) {
  state.categories.push(payload)
}
export function REMOVE_CATEGORY ( state, payload ) {
  state.categories = state.categories.filter(el => el.id != payload)
}
export function SET_LOADER_CATEGORY ( state ) {
  state.categories.ready = false
  state.categories.available = true
}
