
export function SET_ORDERS(state, payload) {

   state.orders = { ...state.orders, ...payload }
   state.orders.ready = true

}
export function SET_PAGINATE_ORDERS(state, payload) {

   state.orders.data = [...state.orders.data, ...payload.data]

}

export function SET_CUSTOMER_ORDERS(state, payload) {

   state.customer_order = { ...state.customer_order, ...payload }
   state.customer_order.ready = true
}
export function SET_PAGINATE_CUSTOMER_ORDERS(state, payload) {

   state.customer_order.data = [...state.customer_order.data, ...payload.data]

}
export function SET_INVOICE(state, payload) {

   state.invoice = payload
}

export function REMOVE_INVOICE(state) {

   state.invoice = null

}
export function SET_ORDER_MENU(state, data) {

   state.order_menu = data

}
export function SET_TRANSACTION(state, payload) {

   state.transaction = payload

}
export function SET_LOAD_MORE(state, status) {

   state.orders.isLoadMore = status

}
export function SET_LOAD_MORE_CUSTOMER(state, status) {

   state.customer_order.isLoadMore = status

}
export function SET_NOTIFY_ORDER_ITEMS(state, payload) {

   state.orderItems = payload

}



