export default function () {
   return {
      items: [],
      service_fee: 0,
      payment: null,
      customer: {
         customer_name: '',
         customer_email: '',
         customer_phone: '',
         shipping_address: '',
      },
      shipping_method: null,
      voucher: null,
      customer_note: '',
   }
}
