<script>
// eslint-disable-next-line no-unused-vars
import { Api } from "boot/axios";
export default {

   data() {
      return {
         fields: [],
         loading: false
      }
   },
   mounted() {
      this.getData()
   },
   methods: {
      getData() {
         Api.get('installer/database').then(res => {
            this.fields = res.data.data

         })
      },
      checkDatabase() {
         this.loading = true
         Api.post('installer/database', this.fields).then(res => {
            let result = res.data.data

            if (result.secret_key) {
               localStorage.setItem('secret_key', result.secret_key)
            }

            if (result.success) {
               this.$emit('onDone', true)
               this.$q.notify({
                  color: 'grey-8',
                  textColor: 'white',
                  message: result.message,
               })
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
         <h2>Setup Database</h2>
      </div>
      <q-form @submit="checkDatabase">
         <q-input required stack-label v-for="(item, key, i) in fields" :key="i" v-model="fields[key]"
            :label="`DB_${key.toUpperCase()}`"></q-input>
         <div class="card-action q-pt-lg">
            <q-btn :loading="loading" class="full-width" type="submit" color="teal">Submit Data</q-btn>
         </div>
      </q-form>
   </div>
</template>