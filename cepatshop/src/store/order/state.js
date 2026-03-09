export default function () {
   return {
      orders: {
         data: [],
         ready: false,
         total: 0,
         from: 1
      },
      customer_order: {
         data: [],
         ready: false,
         total: 0,
      },
      invoice: null,
      transaction: null,
      orderItems: [],
      order_menu: []
   }
}
