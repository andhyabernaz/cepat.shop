export function ADD_ITEM(state, payload) {
   if (!payload) {
      return
   }

   if (state.items.length && state.items[0].product_type != payload.product_type) {
      resetCheckout(state)
   }

   let hasItem = state.items.find(el => el.sku == payload.sku)

   if (hasItem != undefined) {
      let index = state.items.findIndex(el => el.sku == payload.sku)
      state.items[index].quantity += parseInt(payload.quantity || 0)
      state.items[index] = {
         ...state.items[index],
         ...payload,
         quantity: parseInt(state.items[index].quantity || 0),
      }
      return
   }

   state.items.push({
      ...payload,
      quantity: parseInt(payload.quantity || 0),
   })
}

export function REPLACE_ITEMS(state, payload) {
   state.items = Array.isArray(payload) ? payload : []
}

export function UPDATE_ITEM_QTY(state, payload) {
   const index = state.items.findIndex(item => item.sku == payload.sku)
   if (index == -1) {
      return
   }

   state.items[index].quantity = parseInt(payload.quantity || 0)
}

export function REMOVE_ITEM(state, sku) {
   state.items = state.items.filter(item => item.sku != sku)

   if (!state.items.length) {
      resetCheckout(state)
   }
}

export function CLEAR_CHECKOUT(state) {
   resetCheckout(state)
}

export function SET_PAYMENT(state, payload) {
   state.payment = payload
}

export function TOGGLE_VOUCHER(state, payload) {
   if (state.voucher && state.voucher.id == payload.id) {
      state.voucher = null
      return
   }

   state.voucher = payload
}

export function SET_VOUCHER(state, payload) {
   state.voucher = payload
}

export function SET_CUSTOMER(state, payload) {
   state.customer = {
      ...state.customer,
      ...(payload || {}),
   }
}

export function SET_CUSTOMER_FIELD(state, payload) {
   state.customer[payload.key] = payload.value
}

export function SET_SHIPPING_METHOD(state, payload) {
   state.shipping_method = payload
}

export function SET_SERVICE_FEE(state, config) {
   if (config.is_service_fee) {
      state.service_fee = config.service_fee
   } else {
      state.service_fee = 0
   }
}

export function SET_CUSTOMER_NOTE(state, payload) {
   state.customer_note = payload
}

function resetCheckout(state) {
   state.items = []
   state.payment = null
   state.shipping_method = null
   state.voucher = null
   state.customer_note = ''
   state.customer = {
      customer_name: state.customer?.customer_name || '',
      customer_email: state.customer?.customer_email || '',
      customer_phone: state.customer?.customer_phone || '',
      shipping_address: state.customer?.shipping_address || '',
   }
}
