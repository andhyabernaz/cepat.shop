<script>
import Requirements from './AppRequirements.vue'
import Database from './CheckDatabase.vue'
import InstallApp from './InstallApp.vue'
import { Api } from 'boot/axios'
export default {
   components: { Requirements, Database, InstallApp },
   data() {
      return {
         step: 1,
         is_done: true,
         admin_email: '',
         admin_password: '',
      }
   },
   computed: {
      can_install() {
         return this.$store.state.can_install
      }
   },
   created() {
      this.checkStatusInstalled()
   },
   methods: {
      nextStep() {
         this.step++
         this.is_done = false
      },
      prevStep() {
         this.step--
         this.is_done = true
      },
      stepDone() {
         this.is_done = true
      },
      complete(val) {
         this.step++
         this.admin_email = val.email
         this.admin_password = val.password
      },
      checkStatusInstalled() {
         this.$q.loading.show()
         Api.get('installer/status').then(res => {

            if (res.data.installed) {
               this.$router.replace('/')
            } else {

               this.$store.commit('CAN_INSTALL', true)
            }
         }).finally(() => {
            this.$q.loading.hide()
         })
      }
   }
}
</script>


<template>
   <q-layout view="hHh lpR fFf" class="bg-white">
      <q-page-container>
         <q-page class="bg-grey-1">
            <q-header class="q-px-sm">
               <q-toolbar>
                  <q-toolbar-title class="text-weight-bold brand">
                     <span>Cepatshop Installer</span>
                  </q-toolbar-title>
               </q-toolbar>
            </q-header>
            <div v-if="can_install">
               <q-stepper v-model="step" color="primary" animated flat vertical>
                  <q-step :name="1" title="Welcome" icon="home" :done="step > 1">
                     <div class="q-py-sm">
                        <div class="block-title q-mb-md">
                           <h2>Installasi Cepatshop App</h2>
                        </div>
                        <p>
                           Sebelum melanjutkan installasi, silahkan membuat <b>database</b> dan <b>database user</b>
                           terlebih dahulu di server atau panel hosting anda
                        </p>

                        <q-stepper-navigation>
                           <q-btn :color="is_done ? 'primary' : 'grey-6'" :disable="!is_done" @click="nextStep"
                              label="Continue" />
                        </q-stepper-navigation>
                     </div>
                  </q-step>

                  <q-step :name="2" title="Server Requirements" icon="settings" :done="step > 2">
                     <Requirements @onDone="stepDone"></Requirements>
                     <q-stepper-navigation>
                        <q-btn :color="is_done ? 'primary' : 'grey-6'" :disable="!is_done" @click="nextStep"
                           label="Continue" />
                        <q-btn flat @click="prevStep" color="primary" label="Back" class="q-ml-sm" />
                     </q-stepper-navigation>
                  </q-step>

                  <q-step :name="3" title="Setup Database" icon="source" :done="step > 3">
                     <Database @onDone="stepDone"></Database>
                     <q-stepper-navigation>
                        <q-btn :color="is_done ? 'primary' : 'grey-6'" :disable="!is_done" @click="nextStep"
                           label="Continue" />
                        <q-btn flat @click="prevStep" color="primary" label="Back" class="q-ml-sm" />
                     </q-stepper-navigation>
                  </q-step>
                  <q-step :name="4" title="Configure & Install App" icon="input" :done="step > 4">
                     <InstallApp @onDone="complete"></InstallApp>
                     <q-stepper-navigation>
                        <q-btn flat @click="prevStep" color="primary" label="Back" class="q-ml-sm" />
                     </q-stepper-navigation>
                  </q-step>
                  <q-step :name="5" title="Done" icon="check">
                     <div class="q-py-sm">
                        <div class="block-title q-mb-md">
                           <h2>Installasi Selesai</h2>
                        </div>

                        <p>
                           Selamat, Installasi aplikasi berhasil<br>Berikut kredensial anda:
                        </p>
                        <div class="q-pa-md bg-green-1">

                           <div>Admin Email: <b>{{ this.admin_email }}</b></div>
                           <div>Admin Password: <b>{{ this.admin_password }}</b></div>
                        </div>
                        <q-stepper-navigation>
                           <q-btn :to="{ name: 'Login' }" color="primary" label="Login" />

                           <q-btn outline to="/" color="primary" label="Beranda" class="q-ml-sm" />
                        </q-stepper-navigation>
                     </div>
                  </q-step>

               </q-stepper>
            </div>
         </q-page>
      </q-page-container>
   </q-layout>

</template>