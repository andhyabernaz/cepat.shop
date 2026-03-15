
export function SET_ALL_PAGE(state, payload) {
   state.pages = { ...state.pages, ...payload }
   state.pages.ready = true
   state.pages.available = state.pages.data.length ? true : false
}

export function SET_LOADER_PAGE(state) {
   state.pages.ready = false
   state.pages.available = true
}

