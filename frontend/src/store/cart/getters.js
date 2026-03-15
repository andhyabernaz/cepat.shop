export const cartCount = (state) => {
   return state.carts ? state.carts.length : 0
}

export const getCarts = (state) => {
   let subtotal = sumSubtotal(state.carts)
   let carts = {
      items: state.carts,
      subtotal: subtotal,
      weight: sumWeight(state.carts),
      qty: sumQty(state.carts),
   }

   return carts
}

export const getChartOrderForm = (state, getters, rootState) => {

   let subtotal = sumSubtotal(state.carts)
   let weight = sumWeight(state.carts)
   let qty = sumQty(state.carts)
   let shipping_cost = 0
   let unique_code = 0
   if (rootState.config && rootState.config.is_unique_code) {
      unique_code = getUniqueCode(state)
   }
   let service_fee = parseInt(state.service_fee)
   let payment_fee = state.payment ? parseInt(state.payment.payment_fee) : 0

   let total = parseInt(subtotal) + parseInt(shipping_cost) + parseInt(unique_code) + parseInt(service_fee);

   let voucher_discount = getVoucherDiscount(state, subtotal)
   let shipping_discount = 0

   let grandTotal = parseInt(total) - (parseInt(voucher_discount) + parseInt(shipping_discount));
   let billing_total = parseInt(grandTotal) + parseInt(payment_fee)

   let productType = (state.carts && state.carts.length) ? state.carts[0].product_type : null

   return {
      subtotal: subtotal,
      weight: weight,
      qty: qty,
      service_fee: service_fee,
      unique_code: unique_code,
      customer_note: state.customer_note,
      payment_fee: payment_fee,
      shipping_cost: shipping_cost,
      shipping_discount: shipping_discount,
      voucher_discount: voucher_discount,
      total: total,
      grand_total: grandTotal,
      billing_total: billing_total,
      items: state.carts,
      customer: state.customer,
      courier: state.courier,
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

      let discount_amount = state.voucher.discount_amount;
      let max_discount = state.voucher.max_discount_amount

      let calculate_discount = 0

      if (state.voucher.discount_type == 'percent') {
         calculate_discount = (subtotal * discount_amount) / 100;
      } else {
         calculate_discount = parseInt(discount_amount)
      }

      if (max_discount > 0 && calculate_discount > max_discount) {
         current_discount = max_discount
      } else {
         current_discount = calculate_discount
      }
   }
   return parseInt(current_discount)
}
function sumSubtotal(items) {
   let subtotal = 0
   if (items && items.length) {

      if (items.length > 1) {
         let j = [];
         items.forEach(element => {
            j.push(element.quantity * element.price)
         });
         subtotal = j.reduce((a, b) => a + b)
      } else {
         subtotal = parseInt(items[0].quantity) * parseInt(items[0].price)
      }
   }
   return parseInt(subtotal)

}
function sumWeight(items) {
   let weight = 0
   if (items && items.length) {

      if (items.length > 1) {
         let w = [];
         items.forEach(el => {
            w.push(parseInt(el.weight) * parseInt(el.quantity))
         });
         weight = w.reduce((a, b) => a + b)
      } else {

         weight = parseInt(items[0].quantity) * parseInt(items[0].weight)
      }
   }
   return parseInt(weight)

}
function sumQty(items) {
   let qty = 0
   if (items && items.length) {

      if (items.length > 1) {
         let q = [];
         items.forEach(el => {
            q.push(parseInt(el.quantity))
         });
         qty = q.reduce((a, b) => a + b)
      }
      qty = parseInt(items[0].quantity)
   }
   return parseInt(qty)
}

function getUniqueCode(state) {
   let result = 0

   if (state.payment && state.payment.payment_type == 'DIRECT_TRANSFER') {

      let numbers = '012102';
      result += numbers.charAt(Math.floor(Math.random() * numbers.length));
      numbers = '2143';
      result += numbers.charAt(Math.floor(Math.random() * numbers.length));
      numbers = '12342563784910';
      result += numbers.charAt(Math.floor(Math.random() * numbers.length));

   }

   return parseInt(result);
}
