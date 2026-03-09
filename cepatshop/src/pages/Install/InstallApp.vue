<script>
// eslint-disable-next-line no-unused-vars
import { Api } from "boot/axios";
export default {

   data() {
      return {
         loading: false,
         form: {
            shop_name: '',
            shop_phone: '',
            admin_name: '',
            admin_email: '',
            admin_password: '',
            with_demo: false,
            secret_key: ''
         }
      }
   },
   methods: {
      handleInstall(with_demo = false) {
         this.form.with_demo = with_demo

         this.$refs.form.submit()

      },
      installNow() {
         this.loading = true
         this.form.secret_key = localStorage.getItem('secret_key')
         Api.post('installer/install', this.form).then(res => {
            let result = res.data.data

            if (result.success) {
               this.$emit('onDone', { email: this.form.admin_email, password: this.form.admin_password })
               this.$q.notify({
                  color: 'grey-8',
                  textColor: 'white',
                  message: result.message,
               })

               localStorage.removeItem('secret_key')
            }

            if (!result.success) {
               this.$q.notify({
                  type: 'negative',
                  message: result.message,
                  timeout: 6000
               })
            }
         }).finally(() => {
            this.loading = false
         })
      }
   }

}
</script>

<template>
   <div class="q-py-sm">
      <div class="block-title q-mb-md">
         <h2>Configure & Install App</h2>
      </div>
      <form @submit.prevent="installNow">
         <div>
            <q-card class="section" flat bordered>
               <q-card-section class="q-gutter-y-sm">
                  <div class="text-md">Shop Detail</div>
                  <q-input required filled stack-label v-model="form.shop_name" label="Shop Name"></q-input>
                  <q-input required filled stack-label v-model="form.shop_phone" label="Shop Phone"></q-input>
               </q-card-section>
            </q-card>
            <q-card class="section q-mt-md" flat bordered>
               <q-card-section class="q-gutter-y-sm">
                  <div class="text-md">Admin Detail</div>
                  <q-input required filled stack-label v-model="form.admin_name" label="Admin Name"></q-input>
                  <q-input required filled stack-label v-model="form.admin_email" label="Admin Email"></q-input>
                  <q-input required filled stack-label v-model="form.admin_password" label="Admin Password"></q-input>
               </q-card-section>
            </q-card>
            <div class="q-py-md">
               <q-checkbox v-model="form.with_demo" label="Install Demo Konten"></q-checkbox>
            </div>
            <q-btn :loading="loading" class="full-width" type="submit" color="teal">Install Sekarang</q-btn>
         </div>
      </form>
   </div>
</template>