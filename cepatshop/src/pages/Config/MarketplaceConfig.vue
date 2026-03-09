<template>
   <div>
      <q-card flat class="section">
         <q-card-section>
            <div class="flex flex-nowrap justify-between items-start q-mb-md q-gutter-sm">
               <div>
                  <div class="card-subtitle">
                     Pengaturan Marketplace
                  </div>
               </div>
               <q-btn label="Provider" icon="add" color="primary" @click="handleAdd"></q-btn>
            </div>

            <div class="q-mt-md table-responsive">
               <table class="table aligned">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Icon</th>
                        <th>Provider</th>
                        <th>Url Toko (opsional)</th>
                        <th>Status</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, idx) in configs" :key="idx">
                        <td>{{ idx + 1 }}</td>
                        <td>
                           <img :src="item.icon" class="mp_icon">
                        </td>
                        <td>{{ item.provider }}</td>
                        <td>{{ item.url ? item.url : '-' }}</td>
                        <td>
                           <q-btn-group rounded unelevated>
                              <q-btn @click="activated(item, idx)" padding="4px 8px" no-caps size="sm"
                                 :text-color="item.is_active ? 'white' : 'grey-7'"
                                 :color="item.is_active ? 'green' : 'grey-4'" label="Active" />
                              <q-btn @click="inActivated(item, idx)" padding="4px 8px" no-caps size="sm"
                                 :text-color="!item.is_active ? 'white' : 'grey-7'"
                                 :color="!item.is_active ? 'grey-8' : 'grey-4'" label="Inactive" />
                           </q-btn-group>
                        </td>
                        <td class="flex no-wrap q-gutter-xs">
                           <q-btn :disable="item.is_default" size="11px" round icon="delete" @click="handleDelete(item)"
                              color="red"></q-btn>
                           <q-btn size="11px" round icon="edit" @click="handleEdit(item)" color="blue"></q-btn>
                        </td>
                     </tr>

                  </tbody>
               </table>
            </div>

            <div class="q-pa-md q-mt-md bg-yellow-1">
               <q-item-label class="text-yellow-10">* Jika status nonaktif atau url toko kosong maka link marketplace
                  tidak akan
                  ditampilkan dihalaman depan.
               </q-item-label>
               <q-item-label class="text-yellow-10">* Jika status nonaktif maka link marketplace pada produk (jika ada)
                  tidak akan
                  ditampilkan dihalaman produk.</q-item-label>
            </div>

         </q-card-section>
      </q-card>
      <q-dialog v-model="modal">
         <q-card class="card-xl">
            <q-card-section>
               <div class="card-title">Form</div>
               <form @submit.prevent="submitConfig">
                  <div class="q-gutter-y-md q-pb-md">
                     <q-input :readonly="form.is_default" label="Provider" v-model="form.provider" required></q-input>
                     <q-input label="Url Toko (opsional)" v-model="form.url"></q-input>
                     <div>

                        <label for="icon">
                           <div class="text-grey-8 q-pb-xs">{{ form.is_default ? 'Custom Icon' : 'Change Icon' }}</div>
                           <input type="file" :required="form._method == 'POST'" id="icon" accept="images/*"
                              @change="updateFileImage" />
                        </label>
                     </div>
                  </div>
                  <!-- <div class="q-pt-md" style="min-height:100px">
                     <q-btn label="Upload Icon" @click="handleUploaIcon" color="teal" class="btn-action"></q-btn>
                  </div> -->
                  <div class="card-action q-pt-xl">
                     <q-btn type="submit" label="Submit" color="primary"></q-btn>
                     <q-btn type="button" v-close-popup label="Batal" color="primary" outline></q-btn>
                  </div>
               </form>
            </q-card-section>
         </q-card>
      </q-dialog>

   </div>
</template>

<script>
import { BaseApi } from 'boot/axios'
import { Dialog } from 'quasar';
export default {
   data() {
      return {
         modal: false,
         configs: [],
         selected: null,
         form: {
            id: '',
            provider: '',
            is_active: false,
            is_default: false,
            url: '',
            icon: '',
            _method: 'POST'
         }
      }
   },
   created() {
      this.getData()
   },
   methods: {
      getData() {
         BaseApi.get('marketplaces').then((res) => {
            this.configs = res.data.data
         })
      },
      submitConfig() {
         let url = 'marketplaces'
         if (this.form._method == 'PUT') {
            url = `marketplaces/${this.form.id}`
         }

         this.$store.commit('SET_LOADING', true)

         BaseApi.post(url, this.form, {
            headers: { 'Content-Type': 'multipart/form-data' }
         }).then(() => {
            this.$q.notify({
               type: 'positive',
               message: 'Berhasil memperbarui data'
            })
            this.modal = false
            this.getData()
         })
      },
      handleAdd() {
         this.form.provider = ''
         this.form.url = ''
         this.form.icon = ''
         this.form._method = 'POST'

         this.modal = true
      },
      handleEdit(item) {
         this.form = { ...item }
         this.form._method = 'PUT'
         this.form.icon = ''
         this.modal = true
      },
      handleDelete(item) {
         Dialog.create({
            title: 'Konfirmasi Pemghapusan',
            message: 'Data yang dihapus tidak dapat diekbalikan',
            cancel: true
         }).onOk(() => {
            this.$store.commit('SET_LOADING', true)
            BaseApi.delete(`marketplaces/${item.id}`).then(() => {
               this.getData()
            })
         })
      },
      activated(item, idx) {
         if (item.is_active) return
         this.configs[idx].is_active = true

         BaseApi.post(`marketplaces/${item.id}/setStatus`, { status: 1 }).then(() => {
            this.getData()
         })
      },
      updateFileImage(e) {
         const file = e.target.files[0]

         if (file) {
            this.form.icon = file
         } else {
            this.$q.notify({
               type: 'negative',
               message: 'File tidak didukung'
            })
         }
      },
      inActivated(item, idx) {
         if (!item.is_active) return
         this.configs[idx].is_active = false
         BaseApi.post(`marketplaces/${item.id}/setStatus`, { status: 0 }).then(() => {
            this.getData()
         })
      },
   }
}
</script>