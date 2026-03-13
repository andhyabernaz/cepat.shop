
export const getFilterPage = (state) => (status) => {
   if (!status || status === 'all') return state.pages.data
   return state.pages.data.filter((el) => el.status === status)
}

