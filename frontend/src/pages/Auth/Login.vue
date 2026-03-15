<template>
  <q-page class="flex flex-center relative">
    <div class="card-md">

      <q-card flat>
        <q-card-section>
          <!-- <div class="flex justify-center q-mb-md">
            <img @click="$router.push({name: 'Home'})" class="cursor-pointer" v-if="shop && shop.logo_path" :src="shop.logo" style="width:auto;height:75px;object-fit:contain;max-width:150px;" />
            <img @click="$router.push({name: 'Home'})" class="cursor-pointer" v-else src="/icon/icon-192x192.png" style="width:auto;height:75px;object-fit:contain;max-width:150px;" />
          </div> -->

           <div class="block-title">
             <h2>Login</h2>
           </div>
       
          <form @submit.prevent="submit" class="q-gutter-y-sm q-pa-md">
              <q-input
              v-model="form.email"
              color="grey-6"
              label="Email atau No Ponsel *"
              dense
              lazy-rules
              :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
            >
            <template v-slot:prepend>
              <q-icon name="eva-person-outline" />
            </template>
              </q-input>
            
            <q-input 
            v-model="form.password" 
            label="Password Anda *"
            color="grey-6"
            :type="isPwd ? 'password' : 'text'"
            dense
            :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
            >
            <template v-slot:prepend>
              <q-icon name="eva-lock-outline" />
            </template>
          <template v-slot:append>
            <q-icon
              :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'"
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>
          <div class="column">
            <q-btn :loading="isLoading"
            type="submit" color="primary" padding="sm lg"
            >Login</q-btn>
  
          </div>
        </form>
           <div class="text-red q-pb-sm text-center" v-if="errors.email">{{ errors.email[0] }}</div>
          <div class="text-center q-mt-sm">
          Belum punya akun <q-btn no-caps color="primary" padding="xs" flat :disable="isLoading" label="Daftar Disini" :to="{ name: 'Register'}"></q-btn>
          </div>
          <div class="text-center q-mt-sm">
          <q-btn no-caps color="primary" padding="xs" flat :disable="isLoading" label="Lupa password?" :to="{ name: 'ForgotPassword'}"></q-btn>
          </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'PageLogin',
  data () {
    return {
      isPwd: true,
      form: {
        email: '',
        password: '',
        device_name: 'APP'
      }
    }
  },
  computed: {
    errors() {
      return this.$store.state.errors
    },
    isLoading() {
      return this.$store.state.loading
    },
    shop() {
      return this.$store.state.shop
    }
  },
  methods: {
    ...mapActions('user', ['login']),
    submit() {
     this.login({
      ...this.form,
      redirect: this.$route?.query?.redirect || null
     })
    }
  }
}
</script>
