<template>
   <q-page padding>
      <AppHeader title="Pengaturan AKun"></AppHeader>
      <q-card class="section shadow">
         <q-card-section>

            <div class="q-gutter-y-md">
               <q-input filled v-model="form.name" label="Nama">
                  <template v-slot:prepend>
                     <q-icon name="eva-person-outline" />
                  </template>
               </q-input>
               <q-input filled v-model="form.phone" label="No Ponsel / Whatasapp" lazy-rules
                  :rules="[requiredRules, validPhoneRules]">
                  <template v-slot:prepend>
                     <q-icon name="eva-phone-outline" />
                  </template>
               </q-input>
               <q-input filled type="email" v-model="form.email" label="Email">
                  <template v-slot:prepend>
                     <q-icon name="eva-email-outline" />
                  </template>
               </q-input>
               <template v-if="changePassword">
                  <q-input filled :type="isPwd ? 'password' : 'text'" placeholder="Password Baru"
                     v-model="form.password">
                     <template v-slot:prepend>
                        <q-icon name="eva-lock-outline" />
                     </template>
                     <template v-slot:append>
                        <q-icon :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'" class="cursor-pointer"
                           @click="isPwd = !isPwd" />
                     </template>
                  </q-input>
                  <q-input filled :type="isPwd ? 'password' : 'text'" placeholder="Konfirmasi Password"
                     v-model="form.password_confirmation">
                     <template v-slot:prepend>
                        <q-icon name="eva-lock-outline" />
                     </template>
                  </q-input>
               </template>
               <q-btn v-if="!changePassword" @click="btnChangePassword" class="q-mt-md" dense color="primary" no-caps
                  flat label="Ganti Password"></q-btn>
            </div>
            <div class="card-action">
               <q-btn :loading="loading" class="full-width" @click="submit" label="Simpan Data" color="primary">
                  <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
               </q-btn>
            </div>
         </q-card-section>
      </q-card>
   </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { BaseApi } from 'boot/axios'
export default {
   name: 'AccountIndex',
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
         loading: state => state.loading
      })
   },
   created() {
      this.getData()
   },
   methods: {
      ...mapActions('user', ['updateUser']),
      submit() {
         this.$store.commit('SET_LOADING', true)
         this.updateUser(this.form)
      },
      getData() {
         BaseApi.get('user').then(response => {
            if (response.status == 200) {
               this.form.name = response.data.data.name
               this.form.email = response.data.data.email
               this.form.phone = response.data.data.phone
               this.$store.commit('user/SET_USER', response.data.data)
            }
         })
      },
      btnChangePassword() {
         this.changePassword = !this.changePassword
         this.form.password_confirmation = ''
         this.form.password = ''
      }
   },

}
</script>
