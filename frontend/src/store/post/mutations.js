
export function SET_ALL_POST(state, payload) {
   state.posts = { ...state.posts, ...payload }
   state.posts.ready = true
   state.posts.available = state.posts.data.length ? true : false
}

export function SET_LOADER_POST(state) {
   state.posts.ready = false
   state.posts.available = true
}


