export const getById = (state) => (id) => {
  return state.categories.data.find(el => el.id == id)
}

export const categoryCount = (state) => {
  return state.categories.data.length
}
export const getValueOptions = (state) => {
  let data = []
  if(state.categories.data.length > 0) {
    state.categories.data.forEach(el => {
      data.push({label: el.title, value: el.id})
    });
  }
  return data;
}

export const getParentCategories = (state) => {
  let data = []
  if(state.categories.data.length > 0) {
    data = state.categories.data.filter(el => !el.category_id);
  }
  return data;
}
export const getParentCategoryOptions = (state) => {
  let data = []
  if(state.categories.data.length > 0) {
    data = state.categories.data.filter(el => !el.category_id).map(cat => ({ label: cat.title, value: cat.id }));
  }
  return [{ label: 'None', value: ''}, ...data];
}