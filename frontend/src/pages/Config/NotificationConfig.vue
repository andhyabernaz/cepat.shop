<template>
   <div>
       <q-card class="section shadow">
         <q-card-section>
            <div class="flex items-center justify-between">
               <div class="card-subtitle">Konfigurasi SMTP Email</div>
               <!-- <q-toggle left-label color="teal" :label="formEmail.is_active ? 'Active' : 'Disabled'"
                  v-model="formEmail.is_active">
               </q-toggle> -->
            </div>
            <div class="text-caption text-grey-7">Konfigurasi pengiriman notifikasi via email</div>
            <form @submit.prevent="updateMailConfig">
               <div class="q-gutter-y-sm q-mt-md">
                  <q-input filled v-model="formEmail.smtp_host" label="SMTP Host" placeholder="eg: smtp.google.com" />
                  <q-input filled v-model="formEmail.smtp_port" label="SMTP Port" placeholder="eg: 587" />
                  <q-input filled v-model="formEmail.smtp_encryption" label="SMTP Encryption" placeholder="eg: tls" />
                  <q-input filled v-model="formEmail.smtp_username" label="SMTP Username"
                     placeholder="eg: youremail@yourdomain.com" />
                  <q-input filled v-model="formEmail.smtp_password" label="SMTP Password" />
                  <q-input filled v-model="formEmail.from_address" label="SMTP Mail Sender"
                     placeholder="eg: youremail@yourdomain.com" type="email" :rules="[idValidEmail]" lazy-rules>
                  </q-input>
                  <q-input filled v-model="formEmail.from_name" label="Sender Name" placeholder="My Application" />
               </div>
               <div class="q-pt-lg q-gutter-sm">
                  <q-btn class="col" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
                  <q-btn :disable="mailConfig && !mailConfig.is_ready" :loading="isLoading" outline class="col"
                     type="button" label="Test Email" color="primary" @click="sendEmail"></q-btn>
               </div>
            </form>
         </q-card-section>
      </q-card>
      <q-card class="section shadow q-mt-md">
         <q-card-section>
            <div class="flex items-center justify-between">
               <div class="card-subtitle">Telegram Notifikasi</div>
               <div class="q-px-sm rounded-borders text-white"
                  :class="config && config.is_telegram_ready ? 'bg-teal' : 'bg-grey-6'">{{ config &&
                     config.is_telegram_ready ?
                     'Active' : 'Disabled' }}</div>
            </div>
            <div class="text-caption text-grey-7">Notifikasi order untuk admin via telegram</div>
            <div class="text-caption text-grey-7">Silahkan buat bot di telegram untuk mendapatkan token, serta dapatkan
               user
               id di bot @infouserid</div>
            <form @submit.prevent="updateData">
               <div class="q-gutter-y-sm q-mt-md">
                  <q-input filled v-model="form.telegram_bot_token" label="Telegram Bot Token" />
                  <q-input filled v-model="form.telegram_user_id" label="Telegram user Id"
                     placeholder="eg: 1486912253" />
               </div>
               <div class="q-mt-md q-gutter-sm">
                  <q-btn class="col" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
                  <q-btn :disable="!config.is_telegram_ready" :loading="isLoading" outline class="col"
                     type="button" label="Test Telegram" color="primary" @click="sendTelegram"></q-btn>
               </div>
            </form>
         </q-card-section>
      </q-card>
     
   </div>
</template>

<script>
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         form: {
            telegram_bot_token: '',
            telegram_user_id: ''
         },
         isLoading: false,
         mailConfig: null,
         formEmail: {
            smtp_host: '',
            smtp_port: '',
            smtp_username: '',
            smtp_password: '',
            smtp_encryption: '',
            from_address: '',
            from_name: '',
            is_active: false
         }
      }
   },
   watch: {
      'formEmail.is_active': function (val, old) {
         if (val) {
            for (let key in this.formEmail) {
               if (this.formEmail[key] == '' || this.formEmail[key] == null) {
                  this.formEmail.is_active = false
                  this.$q.notify({
                     type: 'negative',
                     message: 'Lengkapi semua form untuk mengaktifkan email'
                  })
                  return
               }
            }
         }
      }
   },
   computed: {
      config: function () {
         return this.$store.state.config
      },
      shop: function () {
         return this.$store.state.shop
      },
   },
   mounted() {
      this.setData()
      this.getMailConfig()
   },
   methods: {
      idValidEmail(val) {
         if (!val) {
            return true
         }
         if (!val.length) {
            return 'Email tidak boleh kosong'
         }

         if (val == this.shop.email) {
            return 'Email tidak boleh sama dengan email toko'
         }

         return true
      },
      sendTelegram() {
         this.isLoading = true
         BaseApi.get('telegram-test')
            .then(res => {
               if (res.status == 200) {
                  this.$q.notify({
                     type: res.data.data.type,
                     message: res.data.data.message
                  })
               }
            }).finally(() => this.isLoading = false)
      },
      sendEmail() {
         this.isLoading = true
         BaseApi.get('email-test')
            .then(res => {
               if (res.status == 200) {
                  this.$q.notify({
                     type: res.data.data.type,
                     message: res.data.data.message
                  })
               }
            }).finally(() => this.isLoading = false)
      },
      updateData() {
         BaseApi.post('config', this.form).then(() => {
            this.$q.notify({
               type: 'positive',
               message: 'Berhasil memperbarui data'
            })
            this.$store.dispatch('getAdminConfig')
         }).catch(() => {
            this.$q.notify({
               type: 'negative',
               message: 'Gagal memperbarui data'
            })
         })
      },
      setData() {
         if (this.config) {
            this.form.telegram_bot_token = this.config.telegram_bot_token
            this.form.telegram_user_id = this.config.telegram_user_id
         }
      },
      getMailConfig() {
         BaseApi.get('config-email').then(res => {
            if (res.status == 200 && res.data.success) {
               this.setEmailConfig(res.data.data)
            }
         })
      },
      setEmailConfig(data) {
         this.mailConfig = data
         this.formEmail.smtp_host = data.smtp_host
         this.formEmail.smtp_port = data.smtp_port
         this.formEmail.smtp_username = data.smtp_username
         this.formEmail.smtp_password = data.smtp_password
         this.formEmail.smtp_encryption = data.smtp_encryption
         this.formEmail.from_address = data.from_address
         this.formEmail.from_name = data.from_name
         this.formEmail.is_active = data.is_active
      },
      updateMailConfig() {
         for (let x in this.formEmail) {
            if (this.formEmail[x] == null || this.formEmail[x] == '') {
               this.formEmail.is_active = false
            }
         }
         BaseApi.post('config-email', this.formEmail).then(res => {
            if (res.status == 200) {
               this.setEmailConfig(res.data.data)
               this.$store.dispatch('getAdminConfig')
               this.$q.notify({
                  type: 'positive',
                  message: 'Berhasil memperbarui data'
               })
            }
         })
      }
   }
}
</script>
