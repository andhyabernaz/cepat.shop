export const getProductById = (state) => (id) => {
  let p = state.products.data.find(el => el.id == id)
  if (p != undefined) {
    return p
  } else {
    return null
  }
}

export const favoriteCount = (state) => {
  return state.favorites ? state.favorites.length : 0
}
export const productCount = (state) => {
  return (state.products && state.products.data) ? state.products.data.length : 0
}
