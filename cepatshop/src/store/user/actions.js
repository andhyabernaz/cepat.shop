import { BaseApi } from 'boot/axios'
import { Notify } from 'quasar'

export function login({ commit, dispatch }, payload) {
   commit('SET_LOADING', true, { root: true })

   BaseApi.post('auth/login', payload)
      .then(response => {
         if (response.status == 200) {
            let data = response.data.data
            commit('SET_USER', data.user)
            commit('SET_TOKEN', data.token)

            if (data.user.is_admin) {
               this.$router.push({ name: 'AdminDashboard' })
            } else {
               this.$router.push({ name: 'CustomerDashboard' })
            }
         }
      })
      .finally(() => {
         commit('SET_LOADING', false, { root: true })
      })

}
export function register({ commit, dispatch }, payload) {
   commit('SET_LOADING', true, { root: true })

   BaseApi.post('auth/register', payload)
      .then(response => {
         if (response.status == 200) {
            let data = response.data.data

            commit('SET_USER', data.user)
            commit('SET_TOKEN', data.token)

            if (data.user.is_admin) {
               this.$router.push({ name: 'AdminDashboard' })
            } else {
               this.$router.push({ name: 'CustomerDashboard' })
            }
         }
      })
      .finally(() => {
         commit('SET_LOADING', false, { root: true })
      })

}

export function getCustomerLicense({ commit }, url = null) {
   if (!url) {
      url = 'licenses'
   }
   BaseApi.get(url).then(response => {
      if (response.status == 200) {
         commit('SET_CUSTOMER_LICENSE', response.data.data)
      }
   })
}
export function getCustomerWithdrawals({ commit }, url = null) {
   if (!url) {
      url = 'customer/withdrawals'
   }
   BaseApi.get(url).then(response => {
      if (response.status == 200) {
         commit('SET_WITHDRAWAL', response.data.data)
      }
   })
}
export function logout({ commit, dispatch }) {
   setTimeout(() => dispatch('getConfig', null, { root: true }), 1000)

   BaseApi.post('auth/logout').finally(() => {
      commit('LOGOUT')
      this.$router.push('/')
   })
}
export function exit({ commit }) {
   commit('LOGOUT')
   this.$router.push('/')
}
export function getUser({ commit }) {
   BaseApi.get('user').then(response => {
      if (response.status == 200) {
         commit('SET_USER', response.data.data)
      }
   }).catch((error) => {
      if (401 === error.response.status) {
         commit('LOGOUT')
         this.$router.push('/')
      }
   })
}
export function getUserPermissions({ commit }) {
   BaseApi.get('getUserPermissions').then(response => {
      commit('SET_PERMISSIONS', response.data.data)
   })
}
export function getUserAddress({ }) {
   return BaseApi.get('user-address')
}
export function updateUser({ commit }, payload) {
   BaseApi.post('user/update', payload).then(response => {
      if (response.status == 200) {
         commit('SET_USER', response.data.data)
         commit('SET_LOADING', false, { root: true })
         Notify.create({
            type: 'positive',
            message: 'Berhasil mengedit data'
         })
      }
   }).catch((error) => {
      commit('SET_LOADING', false, { root: true })
      if (401 === error.response.status) {
         commit('LOGOUT')
         this.$router.push('/')
      }
   })
}

export function requestPasswordToken({ commit }, payload) {
   return BaseApi.post('auth/password-token', payload)
}

export function resetPassword({ }, payload) {

   BaseApi.post('auth/password-reset', payload).then(response => {
      if (response.status == 200 && response.data.success) {
         localStorage.removeItem('is_reqpwd')

         Notify.create({
            type: 'positive',
            message: response.data.message
         })

         this.$router.push({ name: 'Login' })
      }
   })
}

export function validationToken({ commit }) {
   BaseApi.get('auth/validationToken').then(res => {
      commit('SET_HAS_VALIDATION_TOKEN', null ,{ root: true })
      if (res.data.data.is_valid) {
         commit('SET_USER', res.data.data.user)
      } else {
         commit('LOGOUT')
      }
   })
}

