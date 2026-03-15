

export function ADD_TO_CART(state, payload) {

   if (state.carts && state.carts.length) {

      let hasItem = state.carts.find(el => el.sku == payload.sku)

      if (hasItem != undefined) {

         let index = state.carts.findIndex(ej => ej.sku == payload.sku)

         state.carts[index].quantity += payload.quantity

      } else {
         state.carts.push(payload)
      }
   } else {
      state.carts.push(payload)
   }


}

export function UPDATE_CART(state, payload) {

   let objIndex = state.carts.findIndex(el => el.sku == payload.sku)

   state.carts[objIndex].quantity = parseInt(payload.quantity)

}
export function INCREMENT_QTY(state, sku) {

   let objIndex = state.carts.findIndex(el => el.sku == sku)
   let curQty = state.carts[objIndex].quantity

   state.carts[objIndex].quantity = parseInt(curQty) + 1

}
export function DECREMENT_QTY(state, sku) {

   let objIndex = state.carts.findIndex(el => el.sku == sku)
   let curQty = state.carts[objIndex].quantity
   state.carts[objIndex].quantity = parseInt(curQty) - 1

}

export function REMOVE_CART(state, payload) {
   state.carts = state.carts.filter(c => c.sku != payload.sku)
}

export function CLEAR_CART(state) {
   state.carts = []
   state.payment = null
   state.customer = null
   state.courier = null
   state.voucher = null
}
export function CLEAR_CART_ORDER(state) {
   state.payment = null
   state.customer = null
   state.courier = null
   state.voucher = null
}
export function SET_CARTS(state, payload) {
   state.carts = payload
}

export function COMMIT_CARTS(state) {
   localStorage.setItem('_wacommerce-carts', JSON.stringify(state.carts))
}
export function ROLLBACK_CARTS(state) {
   state.carts = JSON.parse(localStorage.getItem('_wacommerce-carts'))
}
export function SET_PAYMENT(state, payload) {
   state.payment = payload
}
export function TOGGLE_VOUCHER(state, payload) {
   if (state.voucher && state.voucher.id == payload.id) {
      state.voucher = null
   } else {
      state.voucher = payload
   }
}

export function SET_VOUCHER(state, payload) {
   state.voucher = payload
}
export function SET_CUSTOMER(state, payload) {
   state.customer = payload
   if (state.payment && state.payment_tyoe != 'DIRECT_TRANSFER') {
      state.payment = null
   }
}
export function SET_COURIER(state, payload) {
   state.courier = payload

   if (payload && payload.courier_code != 'COD') {
      if (state.payment && state.payment.payment_type == 'COD') {
         state.payment = null
      }
   }
}
export function SET_SERVICE_FEE(state, config) {
   if (config.is_service_fee) {
      state.service_fee = config.service_fee
   } else {
      state.service_fee = 0
   }
}

export function SET_CUSTOMER_NOTE(state, data) {
   state.customer_note = data
}