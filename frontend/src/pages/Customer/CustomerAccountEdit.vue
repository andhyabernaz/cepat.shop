<template>
   <q-page padding>
      <AppHeader title="User Akun"></AppHeader>

      <div class="box-column q-gutter-y-md">
         <q-input v-model="form.name" label="Nama">
            <template v-slot:prepend>
               <q-icon name="eva-person-outline" />
            </template>
         </q-input>
         <q-input type="email" v-model="form.email" label="Email">
            <template v-slot:prepend>
               <q-icon name="eva-email-outline" />
            </template>
         </q-input>
         <q-input v-model="form.phone" label="No Ponsel / Whatasapp" lazy-rules
            :rules="[requiredRules, validPhoneRules]">
            <template v-slot:prepend>
               <q-icon name="eva-phone-outline" />
            </template>
         </q-input>
         <template v-if="changePassword">
            <q-input :type="isPwd ? 'password' : 'text'" placeholder="Password Baru" v-model="form.password">
               <template v-slot:prepend>
                  <q-icon name="eva-lock-outline" />
               </template>
               <template v-slot:append>
                  <q-icon :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'" class="cursor-pointer" @click="isPwd = !isPwd" />
               </template>
            </q-input>
            <q-input :type="isPwd ? 'password' : 'text'" placeholder="Konfirmasi Password"
               v-model="form.password_confirmation">
               <template v-slot:prepend>
                  <q-icon name="eva-lock-outline" />
               </template>
               <template v-slot:append>
                  <q-icon :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'" class="cursor-pointer" @click="isPwd = !isPwd" />
               </template>
            </q-input>
         </template>
         <q-btn v-if="!changePassword" @click="btnChangePassword" class="q-mt-md" dense color="primary" no-caps flat
            label="Ganti Password"></q-btn>
      </div>
      <div class="bg-white q-pa-md">
         <q-btn :loading="loading" class="full-width" @click="submit" color="primary" label="Simpan Data">
            <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
         </q-btn>
      </div>
   </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         isPwd: true,
         isPwd1: true,
         changePassword: false,
         form: {
            name: '',
            email: '',
            phone: '',
            password: '',
            password_confirmation: '',
         }
      }
   },
   computed: {
      ...mapState({
         user: state => state.user.user,
         loading: state => state.loading,
      })
   },
   created() {

      if (!this.user) {
         BaseApi.get('user').then(response => {
            if (response.status == 200) {
               this.form.name = response.data.data.name
               this.form.email = response.data.data.email
               this.form.phone = response.data.data.phone
               this.$store.commit('user/SET_USER', response.data.data)
            }
         })
      } else {
         this.form.name = this.user.name
         this.form.email = this.user.email
         this.form.phone = this.user.phone
      }
   },
   methods: {
      ...mapActions('user', ['getUser', 'updateUser']),
      submit() {
         this.$store.commit('SET_LOADING', true)
         this.updateUser(this.form)
      },
      btnChangePassword() {
         this.changePassword = !this.changePassword
         this.form.password_confirmation = ''
         this.form.password = ''
      },
      deleteAkun() {
         this.$q.dialog({
            title: 'Konfirmasi pemghapusan akun',
            message: 'Dengan melakukan aksi ini, akun anda akan dihapus dari database kami dan data tidak dapat dikembalikan.',
            cancel: true,
            ok: { label: 'Hapus Sekarang', flat: true }
         }).onOk(() => {

            BaseApi.delete('customer/deleteAccount').then(res => {
               this.$store.commit('user/LOGOUT')
               this.$q.notify({
                  type: 'positive',
                  message: 'Penghapusan akun berhasil'
               })
               this.$router.push({ name: 'Login' })
            })
         })
      }
   },

}
</script>
