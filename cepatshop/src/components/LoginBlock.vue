<template>
   <q-card flat class="bg-white" style="max-width:420px;width:100%;">
      <div class="q-py-sm q-px-md row justify-between items-center">
         <div class="text-md text-weight-bold text-grey-8">{{ isRegister ? 'Register' : 'Login' }}</div>
         <q-btn @click.prevent="onClose" icon="eva-close" round flat padding="xs"></q-btn>
      </div>
      <q-separator />
      <q-card-section style="max-height:70vh;" class="scroll">
         <q-banner v-if="allErrors.length" class="bg-red-1 text-red-10 q-mb-sm" rounded>
            <div v-for="(err, i) in allErrors" :key="i">{{ err }}</div>
         </q-banner>

         <form @submit.prevent="submit" class="q-gutter-y-xs">
            <template v-if="isRegister">
               <q-input v-model="form.name" color="grey-6" label="Nama Anda *" dense lazy-rules
                  :rules="[requiredRule]">
                  <template v-slot:prepend>
                     <q-icon name="eva-person-outline" />
                  </template>
               </q-input>
               <q-input v-model="form.email" color="grey-6" label="Alamat email *" dense lazy-rules :rules="emailRules">
                  <template v-slot:prepend>
                     <q-icon name="eva-email-outline" />
                  </template>
               </q-input>
               <q-input v-model="form.phone" color="grey-6" label="No Ponsel / Whatsapp *" dense lazy-rules :rules="phoneRules"
                  @update:model-value="normalizePhone">
                  <template v-slot:prepend>
                     <q-icon name="eva-phone-outline" />
                  </template>
               </q-input>
            </template>
            <template v-if="!isRegister">
               <q-input v-model="form.email" color="grey-6" label="Email atau No Ponsel*" dense lazy-rules
                  :rules="[requiredRule]">
                  <template v-slot:prepend>
                     <q-icon name="eva-email-outline" />
                  </template>
               </q-input>
            </template>

            <q-input v-model="form.password" label="Password *" color="grey-6" :type="isPwd ? 'password' : 'text'" dense
               :rules="passwordRules">
               <template v-slot:prepend>
                  <q-icon name="eva-lock-outline" />
               </template>
               <template v-slot:append>
                  <q-icon :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'" class="cursor-pointer" @click="isPwd = !isPwd" />
               </template>
            </q-input>
            <template v-if="isRegister">
               <q-input v-model="form.password_confirmation" label="Konfirmasi Password *" color="grey-6"
                  :type="isPwd ? 'password' : 'text'" dense :rules="passwordConfirmationRules">
                  <template v-slot:prepend>
                     <q-icon name="eva-lock-outline" />
                  </template>
                  <template v-slot:append>
                     <q-icon :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'" class="cursor-pointer"
                        @click="isPwd = !isPwd" />
                  </template>
               </q-input>
            </template>
            <div class="column q-gutter-y-sm">
               <q-btn :loading="isLoading" unelevated type="submit" color="primary" padding="sm lg">{{ isRegister ?
                  'Daftar' :
                  'Login' }}</q-btn>

            </div>
         </form>
         <div class="column q-gutter-y-sm text-center q-mt-xs">
            <div v-if="isRegister">Sudah punya akun <q-btn no-caps color="green-7" padding="xs" flat
                  :disable="isLoading" label="Login Disini" @click="formType = 'login'"></q-btn></div>
            <div v-if="!isRegister">
               <div>
                  Belum punya akun <q-btn no-caps color="green-7" padding="xs" flat :disable="isLoading"
                     label="Daftar Disini" @click="formType = 'register'"></q-btn>
               </div>
               <div>
                  <q-btn no-caps color="green-7" padding="xs" flat :disable="isLoading" label="Lupa password?"
                     :to="{ name: 'ForgotPassword' }"></q-btn>
               </div>
            </div>
         </div>
      </q-card-section>
   </q-card>
</template>

<script>
export default {
   props: {
      initialFormType: {
         type: String,
         default: 'login'
      },
      redirect: {
         default: null
      }
   },
   data() {
      return {
         isPwd: true,
         formType: 'login',
         clientErrors: [],
         form: {
            name: '',
            email: '',
            password: '',
            phone: '',
            password_confirmation: '',
            device_name: 'APP'
         }
      }
   },
   computed: {
      isLoading() {
         return this.$store.state.loading
      },
      isRegister() {
         return this.formType == 'register'
      },
      requiredRule() {
         return (val) => (val && String(val).trim().length > 0) || 'Wajib diisi'
      },
      emailRules() {
         return [
            this.requiredRule,
            (val) => this.isValidEmail(val) || 'Email tidak valid',
         ]
      },
      phoneRules() {
         return [
            this.requiredRule,
            (val) => this.isValidPhone(val) || 'Nomor whatsapp tidak valid',
         ]
      },
      passwordRules() {
         return [
            this.requiredRule,
            (val) => String(val || '').length >= 6 || 'Minimal 6 karakter',
         ]
      },
      passwordConfirmationRules() {
         return [
            this.requiredRule,
            (val) => val === this.form.password || 'Konfirmasi password tidak sama',
         ]
      },
      serverErrors() {
         const errors = this.$store.state.errors || {}
         return Object.keys(errors).flatMap((key) => (Array.isArray(errors[key]) ? errors[key] : [String(errors[key])]))
      },
      allErrors() {
         return [...this.clientErrors, ...this.serverErrors]
      },
   },
   watch: {
      initialFormType: {
         immediate: true,
         handler(val) {
            this.formType = val === 'register' ? 'register' : 'login'
         }
      }
   },
   methods: {
      onClose() {
         this.$emit('onClose');
      },
      isValidEmail(value) {
         const val = String(value || '').trim()
         return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)
      },
      normalizePhone() {
         this.form.phone = String(this.form.phone || '').replace(/\D/g, '')
      },
      isValidPhone(value) {
         const val = String(value || '').replace(/\D/g, '')
         if (val.length <= 9) return false
         return val.startsWith('08') || val.startsWith('628')
      },
      validateClient() {
         const errors = []
         const identity = String(this.form.email || '').trim()
         const password = String(this.form.password || '')

         if (!identity) errors.push('Email / nomor ponsel wajib diisi')
         if (!password) errors.push('Password wajib diisi')
         if (password && password.length < 6) errors.push('Password minimal 6 karakter')

         if (this.isRegister) {
            const name = String(this.form.name || '').trim()
            const email = String(this.form.email || '').trim()
            const phone = String(this.form.phone || '').trim()
            const passwordConfirmation = String(this.form.password_confirmation || '')

            if (!name) errors.push('Nama wajib diisi')
            if (!email) errors.push('Email wajib diisi')
            if (email && !this.isValidEmail(email)) errors.push('Email tidak valid')
            if (!phone) errors.push('Nomor whatsapp wajib diisi')
            if (phone && !this.isValidPhone(phone)) errors.push('Nomor whatsapp tidak valid')
            if (!passwordConfirmation) errors.push('Konfirmasi password wajib diisi')
            if (passwordConfirmation && passwordConfirmation !== password) errors.push('Konfirmasi password tidak sama')
         }

         return errors
      },
      async submit() {
         this.clientErrors = this.validateClient()
         if (this.clientErrors.length) return

         const payload = { ...this.form }
         if (this.redirect) payload.redirect = this.redirect

         try {
            if (this.formType === 'login') {
               await this.$store.dispatch('user/login', payload)
            } else {
               await this.$store.dispatch('user/register', payload)
            }
            this.$emit('onResponse', true)
         } catch (e) {
         }
      }
   }
}
</script>
