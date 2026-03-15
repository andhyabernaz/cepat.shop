export function addItem({ commit, rootState }, payload) {
   commit('ADD_ITEM', payload)

   if (rootState.config) {
      commit('SET_SERVICE_FEE', rootState.config)
   }
}

export function replaceItems({ commit, rootState }, payload) {
   commit('REPLACE_ITEMS', payload)

   if (rootState.config) {
      commit('SET_SERVICE_FEE', rootState.config)
   }
}

export function clearCheckout({ commit }) {
   commit('CLEAR_CHECKOUT')
}
