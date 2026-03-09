<template>
   <q-page padding>
      <AppHeader title="Notification Template Form">
      </AppHeader>

      <q-card class="section shadow" style="max-width:600px">
         <q-card-section>
            <form @submit.prevent="submitData">
               <q-card-section>
                  <div class="notification-template-form">
                     <q-card class="section shadow">
                        <q-card-section>
                           <div class="card-subtitle q-pb-sm">Form</div>
                           <div class="q-gutter-y-sm">
                              <q-select required :options="order_event_options" emit-value map-options outlined
                                 label="Event" v-model="form.event"></q-select>
                              <div>
                                 <div class="text-grey-7">Kirim Via</div>
                                 <q-radio class="q-mr-sm" size="sm" v-model="form.via" v-for="v in via_options"
                                    :key="v.value" :label="v.label" :val="v.value"
                                    :disable="!v.roles.includes(form.role)"></q-radio>

                              </div>
                              <q-input outlined label="Label" required v-model="form.label"></q-input>
                              <q-input outlined label="Subject" required v-model="form.subject"></q-input>
                              <q-input label="Template" required type="textarea" ref="editor" class="preline" rows="8"
                                 outlined v-model="form.template"></q-input>
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
         </q-card-section>
      </q-card>
      <q-dialog v-model="paramModal" position="right" seamless>
         <q-card class="card-lg">
            <div class="q-py-sm q-px-md flex justify-between items-center bg-white sticky-top">
               <div class="text-17 text-weight-medium">Short Code</div>
               <q-btn icon="close" dense round v-close-popup flat size="14px"></q-btn>
            </div>
            <ParamsBlock @onInsert="handleInsert" :params="params"></ParamsBlock>
         </q-card>
      </q-dialog>

   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
import { copyToClipboard } from "quasar";
import ParamsBlock from "./ParamsBlock.vue";
export default {
   components: { ParamsBlock },
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
         paramModal: false,
         form: {
            label: "",
            event: "",
            subject: "",
            template: "",
            role: "Admin",
         },
      };
   },
   computed: {
      config: function () {
         return this.$store.state.config;
      },
      render_templates() {
         if (this.templates.length) {
            return this.templates.filter(el => el.role == this.form.role)
         }

         return []
      }
   },
   mounted() {
      this.getData();

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
         BaseApi
            .get("notification-template")
            .then((response) => {
               if (response.status == 200) {
                  this.setData(response.data.data.templates);
                  this.params = response.data.data.params;
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
      submitNewConfig() {
         BaseApi
            .post("notification-template", this.form)
            .then(() => {
               this.$router.push({ name: 'NotificationTemplate' });
            });
      },
      setData(data) {
         this.templates = data;
      },
      formatTitle(item) {
         let str = item.replace(/[^\w ]/, "");

         return str.split("_").join(" ");
      },
      formatItem(item) {
         return `{{ ${item} }}`;
      },
      showNotify(error = "") {
         if (error) {
            this.$q.notify({
               type: "negative",
               message: "Gagal memperbarui data",
            });
         } else {
            this.$q.notify({
               type: "positive",
               message: "Berhasil memperbarui data",
            });
         }
      },
      handleCopyParam(item) {
         copyToClipboard(`{{ ${item} }}`).then(() => {
            this.$q.notify({
               type: "positive",
               message: `Coppied ${item.split("_").join(" ")} parameter`,
               timeout: 5,
            });
         });
      },
      handleAddNewNotif() {
         this.clearForm();
         this.formModal = true;
      },
      clearForm() {
         this.form.event = "order_created";
         this.form.label = "";
         this.form.subject = "";
         this.form.template = "";
      },
      handleUpdate(item) {
         BaseApi
            .put("notification-template/" + item.id, item)
            .then(() => {
               this.getData();
               this.showNotify()
            });
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
