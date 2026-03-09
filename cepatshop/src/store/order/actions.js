import { Api, BaseApi } from 'boot/axios'

export function getOrders({ commit }, url = 'orders') {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get(url).then(response => {
      if (response.status == 200) {
         commit('SET_ORDERS', response.data.data)
      }
   }).finally(() => commit('SET_LOADING', false, { root: true }))
}
export function getOrder({ }, id) {
   return BaseApi.get(`orders/${id}`)
}

export function getStatusOptions({ commit }) {
   BaseApi.get(`order/status-options`).then(res => {
      commit('SET_ORDER_MENU', res.data.data)
   })
}
export function getOrderByRef({ }, ref) {
   return Api.get('invoice/' + ref)
}
export function getCustomerOrders({ commit }, url = 'customer/orders') {
   commit('SET_LOADING', true, { root: true })
   BaseApi.get(url).then(response => {
      if (response.status == 200) {
         commit('SET_CUSTOMER_ORDERS', response.data.data)
      }
   }).finally(() => {
      commit('SET_LOADING', false, { root: true })
   })
}

export function updateOrder({ dispatch }, payload) {
   BaseApi.post('orders/' + payload.id, payload).then(response => {
      if (response.status == 200) {
         dispatch('getOrders')
      }
   })
}
export function destroyOrder({ }, id) {
   return BaseApi.delete('orders/' + id)
}
export function statusOptions({ }) {
   return BaseApi.get('order/status-options')
}
export function acceptPayment({ }, id) {
   return BaseApi.post(`orders/${id}/accept-payment`)
}
export function shippingOrder({ }, id) {
   return BaseApi.post(`orders/${id}/ship`)
}
export function completionOrder({ }, id) {
   return BaseApi.post(`orders/${id}/complete`)
}
export function cancelOrder({ }, params) {
   return BaseApi.post(`orders/${params.order_id}/cancel`, params)
}

export function inputResi({ }, params) {
   return BaseApi.post(`orders/${params.order_id}/input-resi`, params)
}
export function updateStatusOrder({ }, params) {
   return BaseApi.post(`order/${params.id}/update-status`, params)
}

export function getInvoice({ }, order_ref) {
   return Api.get('invoice/' + order_ref)
}
export function storeOrder({ }, payload) {
   return Api.post('storeorder', payload)
}
export function shippingWaybill({ }, waybill) {
   return Api.get('shipping/tracking/' + waybill)
}