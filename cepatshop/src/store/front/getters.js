export const getProductsByCategory =  (state) => (id) => {

  return state.products.find(data => data.category_id == id)
}

export function getBanner1 (state) {
  if(state.blocks.banner.length) {
    return 
  }
  return null
}

export function getSliderCount (state) {
  return state.sliders.count
}

