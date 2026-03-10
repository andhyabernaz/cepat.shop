export function SET_USER(state, payload) {
   state.user = payload
   state.loggedUser = true
   state.permissions = payload.permissions

   if (payload.address) {
      state.address = payload.address
      localStorage.setItem('user_addresses', JSON.stringify(state.address))
   }
}
export function SET_PERMISSIONS(state, payload) {
   state.permissions = payload
}
export function SET_TOKEN(state, token) {
   state.token = token
}
export function SET_CUSTOMER_LICENSE(state, payload) {
   state.customer_licenses = { ...payload }
}
export function SET_WITHDRAWAL(state, payload) {
   state.customer_withdrawal = { ...payload }
}
export function SET_USER_ADDRESS(state, payload) {
   state.address = payload
   localStorage.setItem('user_addresses', JSON.stringify(state.address))
}
export function DELETE_ADDRESS(state, id) {
   state.address = state.address.filter(el => el.id != id)

   localStorage.setItem('user_addresses', JSON.stringify(state.address))
}
export function PUSH_ADDRESS(state, payload) {
   if (payload.is_primary) {
      for (let i = 0; i < (state.address ? state.address.length : 0); i++) {
         state.address[i].is_primary = false
      }
   }
   const idx = state.address.findIndex(el => el.id == payload.id)
   if (idx >= 0) {
      state.address[idx] = payload
   } else {

      state.address.unshift(payload)
   }
   localStorage.setItem('user_addresses', JSON.stringify(state.address))
}

export function LOGOUT(state) {
   state.user = false
   state.token = null
   state.loggedUser = false
   state.address = []

   localStorage.removeItem('user_address');
   localStorage.removeItem('user_address_1');
   localStorage.removeItem('user_address_2');
   localStorage.removeItem('user_addresses');
}
