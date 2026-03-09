export default function () {
   return {
      loggedUser: false,
      token: null,
      user: null,
      permissions: [],
      address: JSON.parse(localStorage.getItem('user_addresses')) || [],
      customer_licenses: {
         data: [],
         count: 0,
         total: 0,
         available: true
      },
      customer_withdrawal: {
         data: [],
         count: 0,
         total: 0,
         from: 1
      },
   }
}
