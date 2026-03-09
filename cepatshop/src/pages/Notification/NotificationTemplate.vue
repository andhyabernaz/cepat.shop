<template>
   <q-page padding>
      <AppHeader title="Notification Template">

      </AppHeader>
      <MenuTabs></MenuTabs>
      <div>
         <q-card class="box-shadow">
            <q-card-section>
               <div class="q-gutter-sm q-mb-md">
                  <q-btn unelevated v-for="role in roles" :key="role" :outline="form.role != role" color="primary"
                     no-caps @click="form.role = role">{{ `${role} Template` }}</q-btn>
               </div>
               <!-- <q-select class="q-mb-sm" filled label="Role Tipe" :options="roles" v-model="form.role"></q-select> -->

               <div class="flex justify-between items-center q-pa-sm q-gutter-sm q-mb-md">
                  <div class="">
                     <div class="text-subtitle1 text-weight-bold">Notifikasi {{ form.role }}</div>
                     <div class="text-grey-8 text-caption" v-if="form.role == 'Admin'">
                        Pengaturan notifikasi admin, Semua pesan ke admin akan dikirim ke email dan nomor whatsapp toko
                     </div>
                     <div class="text-grey-8 text-caption" v-else>
                        Pengaturan notifikasi customer
                     </div>
                  </div>
                  <q-btn v-if="$can('create-notification')" icon="add" label=" Notifikasi" @click="handleAddNewNotif" color="primary"></q-btn>
               </div>
               <q-separator></q-separator>
               <q-list separator class="list-draggable">
                  <VueDraggableNext v-model="selected_templates" @change="updateSort" handle=".q-item__section--avatar">
                     <q-expansion-item v-for="(item, idx) in selected_templates" :key="idx" :label="item.label"
                        icon="drag_indicator" expand-icon-toggle
                        :caption="`${item.event.split('_').join(' ')} - ${item.via}`" header-class="text-capitalize"
                        group="admin">
                        <div class="q-px-md q-py-sm bg-grey-2">
                           <table>
                              <tbody>
                              <tr>
                                 <th class="q-py-xs q-px-sm text-right" style="vertical-align:baseline;">Event</th>
                                 <td class="q-py-xs q-px-sm text-uppercase">{{ item.event.split('_').join(' ') }}</td>
                              </tr>
                              <tr>
                                 <th class="q-py-xs q-px-sm text-right" style="vertical-align:baseline;">Kirim Via</th>
                                 <td class="q-py-xs q-px-sm text-uppercase">{{ item.via }}</td>
                              </tr>
                              <tr>
                                 <th class="q-py-xs q-px-sm text-right" style="vertical-align:baseline;">Subject</th>
                                 <td class="q-py-xs q-px-sm" style="vertical-align:baseline;">{{ item.subject }}</td>
                              </tr>
                              <tr>
                                 <th class="q-py-xs q-px-sm text-right" style="vertical-align:baseline;">Template</th>
                                 <td class="q-py-xs q-px-sm">
                                    <div class="preline" v-html="item.template"></div>
                                 </td>
                              </tr>
                              </tbody>
                           </table>

                           <div class="card-action justify-end">
                              <q-btn v-if="$can('delete-notification')" label="Delete" class="btn-action" color="red" unelevated
                                 @click="handleDelete(item)"></q-btn>
                              <q-btn v-if="$can('update-notification')" label="Edit" class="btn-action" color="blue" unelevated
                                 @click="handleEdit(item)"></q-btn>
                           </div>
                        </div>
                     </q-expansion-item>
                  </VueDraggableNext>
                  <div class="text-xs q-pa-xs text-grey-7" v-if="selected_templates.length">*Drag untuk mengurutkan
                  </div>
                  <div class="text-center q-py-md" v-if="!selected_templates.length">Tidak ada data</div>
               </q-list>
            </q-card-section>

         </q-card>

         <q-dialog v-model="formModal" persistent no-shake maximized transition-show="slide-up"
            transition-hide="slide-down">
            <q-card class="bg-grey-2">
               <q-bar class="bg-grey-8 text-white">
                  <div class="text-18"> Notifikasi {{ form.role }}</div>
                  <q-space></q-space>
                  <q-btn dense flat icon="close" v-close-popup>
                  </q-btn>
               </q-bar>
               <form @submit.prevent="submitData">
                  <q-card-section>
                     <div class="notification-template-form">
                        <q-card class="section shadow">
                           <q-card-section>
                              <!-- <div class="card-subtitle q-pb-sm">Form</div> -->
                              <div class="q-gutter-y-sm">
                                 <q-select filled required :options="order_event_options" emit-value map-options
                                    label="Event" v-model="form.event"></q-select>
                                 <div>
                                    <div class="text-grey-7">Kirim Via</div>
                                    <q-radio class="q-mr-sm" size="sm" v-model="form.via" v-for="v in via_options"
                                       :key="v.value" :label="v.label" :val="v.value"
                                       :disable="!v.roles.includes(form.role)"></q-radio>

                                 </div>
                                 <q-input filled label="Label" required v-model="form.label"></q-input>
                                 <q-input filled label="Subject" required v-model="form.subject"></q-input>
                                 <q-input label="Template" required type="textarea" ref="editor" class="preline"
                                    rows="8" filled v-model="form.template"></q-input>
                              </div>
                              <div class="card-action">
                                 <q-btn label="Submit Data" type="submit" color="primary" unelevated></q-btn>
                                 <q-btn label="Cancel" color="primary" outline v-close-popup></q-btn>
                              </div>
                           </q-card-section>
                        </q-card>
                        <q-card class="section shadow">
                           <q-card-section>
                              <ParamsBlock :params="params" @onInsert="handleInsert"></ParamsBlock>
                           </q-card-section>
                        </q-card>
                     </div>
                  </q-card-section>
               </form>
            </q-card>
         </q-dialog>
      </div>
   </q-page>
</template>

<script>
import MenuTabs from './MenuTabs.vue'
import { BaseApi } from "boot/axios";
import ParamsBlock from "./ParamsBlock.vue";
import { VueDraggableNext } from 'vue-draggable-next'
export default {
   components: { ParamsBlock, MenuTabs, VueDraggableNext },
   data() {
      return {
         roles: ['Admin', 'Customer'],
         adminConfig: [],
         customerConfig: [],
         templates: [],
         params: [],
         order_status_options: [],
         order_event_options: [],
         formModal: false,
         via_options: [],
         form: {
            _method: '',
            label: "",
            via: '',
            event: "order_created",
            subject: "",
            template: "",
            role: "Admin",
         },
         selected_templates: []
      };
   },
   watch: {
      'form.role': {
         immediate: true,
         handler(value) {
            this.selected_templates = this.templates.filter(el => el.role == value)
         }
      }
   },
   computed: {
      config: function () {
         return this.$store.state.config;
      },
   },
   created() {
      this.getData();
   },
   mounted() {
       this.$canAccess('view-notification')
   },
   methods: {
      handleInsert(text) {
         let editor = this.$refs.editor

         // this.paramModal = false
         this.$nextTick(function () {

            editor.focus();

            document.execCommand("insertText", false, text)


         })
      },
      getData() {
         this.$store.commit('SET_LOADING', true)
         BaseApi
            .get("notification-template")
            .then((response) => {
               if (response.status == 200) {
                  this.templates = response.data.data.templates
                  this.params = response.data.data.params;
                  this.via_options = response.data.data.via;

                  this.selected_templates = this.templates.filter(el => el.role == this.form.role)
               }
            })
         BaseApi
            .get("notification-template/order-options")
            .then((response) => {
               if (response.status == 200) {
                  this.order_status_options = [{ label: 'Pilih', value: '' }, ...response.data.data.order_status_options];
                  this.order_event_options = response.data.data.order_event_options;
               }
            });
      },
      updateSort() {
         BaseApi.post('notification-template/sort', {
            templates: this.selected_templates
         }).then(() => {
            this.getData()
         })
      },
      submitData() {
         this.$store.commit('SET_LOADING', true)
         if (this.form.id) {
            this.form._method = 'PUT'
            BaseApi
               .post("notification-template/" + this.form.id, this.form)
               .then(() => {
                  this.getData();
                  this.formModal = false;
               });

         } else {
            this.form._method = 'POST'
            BaseApi
               .post("notification-template", this.form)
               .then(() => {
                  this.getData();
                  this.formModal = false;
               });
         }
      },
      handleAddNewNotif() {
         this.clearForm();
         this.formModal = true;
      },
      clearForm() {
         this.form.event = "order_created";
         this.form.id = null
         this.form.label = "";
         this.form.subject = "";
         this.form.template = "";
         this.form.via = "";
      },
      handleEdit(item) {
         this.form = { ...item }
         this.formModal = true
      },
      handleDelete(item) {
         this.$q
            .dialog({
               title: "Konfirmasi",
               message: "Yakin akan mneghapus data?",
               cancel: true,
               ok: { flat: true, label: "Hapus" },
            })
            .onOk(() => {
               BaseApi
                  .delete("notification-template/" + item.id)
                  .then(() => {
                     this.getData();
                  });
            });
      },
   },
};
</script>
