export const getChartOrderForm = (state, getters, rootState) => {
   const items = state.items || []
   const subtotal = sumSubtotal(items)
   const weight = sumWeight(items)
   const qty = sumQty(items)
   const shipping_cost = state.shipping_method ? parseInt(state.shipping_method.price || 0) : 0

   let unique_code = 0
   if (rootState.config && rootState.config.is_unique_code) {
      unique_code = getUniqueCode(state)
   }

   const service_fee = parseInt(state.service_fee || 0)
   const payment_fee = state.payment ? parseInt(state.payment.payment_fee || 0) : 0
   const voucher_discount = getVoucherDiscount(state, subtotal)
   const shipping_discount = getShippingDiscount(state, shipping_cost)
   const total = subtotal + shipping_cost + unique_code + service_fee
   const grand_total = total - (voucher_discount + shipping_discount)
   const billing_total = grand_total + payment_fee
   const productType = items.length ? items[0].product_type : null

   return {
      subtotal,
      weight,
      qty,
      service_fee,
      unique_code,
      customer_note: state.customer_note,
      payment_fee,
      shipping_cost,
      shipping_discount,
      voucher_discount,
      total,
      grand_total,
      billing_total,
      items,
      customer: state.customer,
      courier: state.shipping_method,
      payment: state.payment,
      voucher: state.voucher,
      product_type: productType,
      is_default: productType == 'Default',
      is_digital: productType != 'Default',
      is_deposit: productType == 'Deposit',
   }
}

function getVoucherDiscount(state, subtotal) {
   let current_discount = 0

   if (state.voucher && state.voucher.is_type_shipping == false) {
      let discount_amount = parseInt(state.voucher.discount_amount || 0)
      let max_discount = parseInt(state.voucher.max_discount_amount || 0)
      let calculate_discount = 0

      if (state.voucher.discount_type == 'percent') {
         calculate_discount = Math.floor((subtotal * discount_amount) / 100)
      } else {
         calculate_discount = discount_amount
      }

      if (max_discount > 0 && calculate_discount > max_discount) {
         current_discount = max_discount
      } else {
         current_discount = calculate_discount
      }
   }

   return parseInt(current_discount)
}

function getShippingDiscount(state, shippingCost) {
   let current_discount = 0

   if (state.voucher && state.voucher.is_type_shipping == true) {
      let discount_amount = parseInt(state.voucher.discount_amount || 0)
      let max_discount = parseInt(state.voucher.max_discount_amount || 0)
      let calculate_discount = 0

      if (state.voucher.discount_type == 'percent') {
         calculate_discount = Math.floor((shippingCost * discount_amount) / 100)
      } else {
         calculate_discount = discount_amount
      }

      current_discount = shippingCost > 0 ? calculate_discount : 0

      if (max_discount > 0 && current_discount > max_discount) {
         current_discount = max_discount
      }

      if (current_discount > shippingCost) {
         current_discount = shippingCost
      }
   }

   return parseInt(current_discount)
}

function sumSubtotal(items) {
   if (!items || !items.length) {
      return 0
   }

   return parseInt(items.reduce((total, item) => {
      return total + (parseInt(item.quantity || 0) * parseInt(item.price || 0))
   }, 0))
}

function sumWeight(items) {
   if (!items || !items.length) {
      return 0
   }

   return parseInt(items.reduce((total, item) => {
      return total + (parseInt(item.weight || 0) * parseInt(item.quantity || 0))
   }, 0))
}

function sumQty(items) {
   if (!items || !items.length) {
      return 0
   }

   return parseInt(items.reduce((total, item) => {
      return total + parseInt(item.quantity || 0)
   }, 0))
}

function getUniqueCode(state) {
   let result = 0

   if (state.payment && state.payment.payment_type == 'DIRECT_TRANSFER') {
      let numbers = '012102'
      result += numbers.charAt(Math.floor(Math.random() * numbers.length))
      numbers = '2143'
      result += numbers.charAt(Math.floor(Math.random() * numbers.length))
      numbers = '12342563784910'
      result += numbers.charAt(Math.floor(Math.random() * numbers.length))
   }

   return parseInt(result)
}
