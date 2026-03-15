<template>
   <div class="">
      <q-card class="section shadow">
         <q-card-section>
            <div class="flex flex-nowrap justify-between items-start q-mb-md q-gutter-sm">
               <div>
                  <div class="card-subtitle">
                     Whatsapp Gateway
                  </div>
                  <div class="text-grey-7 text-caption">
                     <div>
                        Dapatkan kredensial api token dari layanan whatsapp gateway yang anda gunakan.
                     </div>
                  </div>
               </div>
               <q-btn label="Provider" icon="add" color="primary" @click="handleAdd"></q-btn>
            </div>


            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['#', 'Provider', 'Active', 'Endpoint', 'Action']" :key="h">{{ h }}
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, i) in vendors" :key="i">
                        <td>{{ i + 1 }}</td>
                        <td>{{ item.provider }}</td>
                        <td>
                           <q-badge :color="item.is_active ? 'green' : 'grey-6'">{{ item.is_active ? 'Active' :
                              'Inactive' }}</q-badge>
                        </td>
                        <td>{{ item.endpoint }}</td>
                        <td>
                           <q-btn-dropdown label="Action" no-caps color="teal" padding="2px 8px" unelevated auto-close>
                              <q-list separator>
                                 <q-item clickable @click="handleEdit(item)">
                                    <q-item-section>Edit</q-item-section>
                                 </q-item>
                                 <q-item clickable @click="activatedService(item)">
                                    <q-item-section>{{ item.is_active ? 'Inactivated' : 'Activated'
                                       }}</q-item-section>
                                 </q-item>
                                 <q-item clickable @click="testing(item)">
                                    <q-item-section>Testing</q-item-section>
                                 </q-item>
                                 <q-item clickable @click="handleDelete(item)" v-if="!item.is_default">
                                    <q-item-section>Hapus</q-item-section>
                                 </q-item>
                              </q-list>
                           </q-btn-dropdown>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </q-card-section>

      </q-card>

      <q-dialog v-model="modal">
         <q-card class="card-xl">
            <q-card-section>
               <div class="card-subtitle flex justify-between items-center">
                  <div>Form Wagateway</div>
                  <q-btn icon="close" round flat v-close-popup dense></q-btn>
               </div>
               <q-form @submit.prevent="submitData">
                  <div v-if="form" style="height:60vh" class="scroll">
                     <div class="q-gutter-y-sm">
                        <q-input :readonly="form.is_default == 1" outlined required label="Provider Name" stack-label
                           v-model="form.provider"></q-input>
                        <q-select outlined v-model="form.content_type" :options="content_types" stack-label
                           label="Content-Type"></q-select>
                        <q-select outlined v-model="form.default_auth" :options="default_auth" emit-value map-options
                           stack-label label="Authorization (optional)"></q-select>
                        <q-input outlined label="Api Key / Api Token" v-model="form.apikey" stack-label></q-input>
                        <q-input outlined required label="URL Endpoint" v-model="form.endpoint" stack-label></q-input>

                        <div class="q-mt-sm">
                           <div class="flex no-wrap justify-between items-end q-gutter-x-sm">
                              <div>
                                 <div class="text-md text-weight-bold">Parameter</div>
                                 <div class="text-grey-7">
                                    Sesuaikan parameter dengan dokumentasi layanan yang anda
                                    gunakan. Perhatikan huruf besar dan huruf
                                    kecil
                                 </div>
                              </div>
                              <q-btn no-caps label=" Add Field" color="teal" class="btn-action" unelevated
                                 @click="handleAddParam"></q-btn>
                           </div>
                           <div class="table-responsive q-mt-md">
                              <table>
                                 <tr v-for="(param, paramsIdx) in form.params" :key="paramsIdx">
                                    <td style="padding:2px">
                                       <q-select style="min-width:160px" stack-label outlined label="Param Type"
                                          v-model="form.params[paramsIdx].param_type"
                                          :options="['HEADER', 'BODY']"></q-select>
                                    </td>
                                    <td style="padding:2px">
                                       <q-input style="min-width:160px" stack-label required outlined label="Param Key"
                                          v-model="form.params[paramsIdx].param_key"></q-input>
                                    </td>
                                    <td style="padding:2px">
                                       <q-input style="min-width:160px" stack-label required outlined
                                          label="Param Value" v-model="form.params[paramsIdx].param_value"></q-input>
                                    </td>

                                    <td class="q-pa-sm">
                                       <q-btn no-caps :disable="form.params.length == 1" icon="delete" color="red" dense
                                          unelevated @click="form.params.splice(paramsIdx, 1)"></q-btn>
                                    </td>

                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                     <div class="q-pa-md bg-grey-2 q-mt-md">
                        <div class="text-sm q-mb-md">Available Default Params</div>
                        <q-list separator bordered dense>
                           <q-item v-for="p in default_params" :key="p.value">
                              <q-item-section>{{ p.label }}</q-item-section>
                              <q-item-section>{{ p.value }}</q-item-section>
                              <q-item-section side>
                                 <q-btn label="Salin" @click="copyString(p.value)" flat dense color="blue"></q-btn>
                              </q-item-section>
                           </q-item>
                        </q-list>
                     </div>
                  </div>
                  <div class="card-action">
                     <q-btn unelevated type="submit" label="Simpan" color="blue"></q-btn>
                  </div>
               </q-form>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   data() {
      return {
         default_auth: [
            { label: 'None', value: null },
            { label: 'Bearer', value: 'Bearer' },
            { label: 'Basic', value: 'Basic' },
         ],
         content_types: ['json', 'form'],
         default_params: [
            { label: 'Apikey', value: '{{ apikey }}' },
            { label: 'Message', value: '{{ message }}' },
            { label: 'Phone', value: '{{ phone }}' }
         ],
         form: {
            id: '',
            is_default: 0,
            provider: '',
            endpoint: '',
            content_type: 'json',
            apikey: '',
            is_active: false,
            default_auth: null,
            params: []

         },
         vendors: [],
         modal: false
      };
   },
   computed: {
      loading() {
         return this.$store.state.loading;
      },
      vendor_options() {
         if (this.form.length) {
            return this.form.map(el => el.provider)
         }
         return []
      }
   },
   created() {
      this.getData();
   },
   methods: {
      testing(item) {
         if (!item.provider || !item.apikey || !item.params.length) {
            this.$q.notify({
               type: 'warning',
               message: 'Konfigurasi belum sesuai'
            })

            return
         }
         this.$store.commit("SET_LOADING", true);
         BaseApi
            .post("wagateway-config/testing/" + item.id)
            .then(() => {
               this.$q.notify({
                  type: "positive",
                  message: "Testing successfully",
               });

            })
            .catch((err) => {
               console.log(err);
            })
            .finally(() => this.$store.commit("SET_LOADING", false));
      },
      handleEdit(item) {
         this.form = { ...item }
         this.modal = true
      },
      handleDelete(item) {
         this.$q.dialog({
            title: 'Konfirmasi',
            message: 'Data yang dihapus tidak dapat diembalikan',
            cancel: true
         }).onOk(() => {
            BaseApi.delete('wagateway-config/' + item.id).then(() => {
               this.getData()
            })
         })
      },
      handleAdd() {
         this.form.id = ''
         this.form.provider = ''
         this.form.endpoint = ''
         this.form.apikey = ''
         this.form.content_type = 'json'
         this.form.default_auth = null
         this.form.is_active = false
         this.form.is_default = 0
         this.form.params = [
            {
               param_type: 'HEADER',
               param_key: '',
               param_value: ''
            }
         ]
         this.modal = true
      },
      canActivated(item) {
         if (item.provider && item.apikey && item.is_active == false && item.params.length) {
            return true
         }
         return false
      },
      formatParams(item) {
         return `{{ ${item} }}`;
      },
      submitData() {
         this.$store.commit("SET_LOADING", true);

         let url = 'wagateway-config'
         this.form._method = 'POST'

         if (this.form.id) {
            url += `/${this.form.id}`
            this.form._method = 'PUT'
         }
         BaseApi
            .post(url, this.form)
            .then(() => {
               this.modal = false
               this.getData();
            })
      },
      activatedService(item) {

         if (!item.is_active && !this.canActivated(item)) {
            this.$q.notify({
               type: 'warning',
               message: 'Konfigurasi belum sesuai'
            })

            return
         }
         this.$store.commit("SET_LOADING", true);
         BaseApi
            .post("wagateway-config/setAsDefault/" + item.id)
            .then(() => {
               this.getData();
            })
            .finally(() => this.$store.commit("SET_LOADING", false));
      },
      getData() {
         this.$store.commit("SET_LOADING", true);
         BaseApi
            .get("wagateway-config")
            .then((res) => {
               if (res.status == 200) {
                  this.vendors = res.data.data;

               }
            })
            .finally(() => this.$store.commit("SET_LOADING", false));
      },
      handleAddParam() {
         let param = {
            param_type: "BODY",
            param_key: "",
            param_value: "",
         };

         this.form.params.push(param);
      },
   },
};
</script>