<template>
   <q-page padding>
      <AppHeader title="Produk Promo">
         <q-btn v-if="$can('create-promo')" color="white" text-color="dark" @click="handleAdd" icon="add" label="Promo"></q-btn>
      </AppHeader>
      <div>
         <div class="bg-white">
            <q-tabs v-model="status" align="left" indicator-color="primary">
               <q-tab v-for="status in statuses" :key="status.value" :name="status.value"
                  @update:model-value="selectTab">{{ status.label }}</q-tab>
            </q-tabs>
         </div>
      </div>
      <div class="box-column flat q-mt-md">
         <div>
            <q-list separator>
               <q-item v-for="(item, index) in promos" :key="index">
                  <q-item-section side top>{{ index + 1 }}</q-item-section>
                  <q-item-section>
                     <q-item-label class="text-md">{{ item.label }} </q-item-label>
                     <q-item-label caption v-if="item.status != 'expired'">{{ item.products_count }}
                        Produk</q-item-label>
                     <q-item-label caption>{{ dateFormat(item.start_date, { hour: 'numeric', minute: 'numeric' }) }} -
                        {{ dateFormat(item.end_date, { hour: 'numeric', minute: 'numeric' }) }}</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <div class="flex no-wrap q-gutter-xs">
                        <q-btn v-if="$can('delete-promo')" size="11px" @click="handleDelete(item.id)" color="red" icon="delete" round>
                           <q-tooltip>Hapus Promo</q-tooltip>
                        </q-btn>
                        <q-btn  v-if="item.status != 'expired' && $can('update-promo')" size="11px" @click="handleEdit(item)" color="blue"
                           icon="edit" round>
                           <q-tooltip>Edit Promo</q-tooltip>
                        </q-btn>
                        <q-btn v-if="item.status != 'expired' && $can('update-promo')" size="11px" @click="
            $router.push({
               name: 'PromoDetail',
               params: { id: item.id },
            })
            " color="teal" icon="widgets" round>
                           <q-tooltip>Edit Produk List</q-tooltip>
                        </q-btn>
                     </div>
                  </q-item-section>
               </q-item>

               <q-item v-if="!promos.length">
                  <q-item-section class="text-center">
                     Tidak ada data
                  </q-item-section>
               </q-item>
            </q-list>
         </div>
      </div>
      <q-dialog v-model="modal" persistent>
         <q-card class="card-medium">
            <div class="card-heading">{{ formType }} Promo</div>
            <form @submit.prevent="submit">
               <q-card-section class="q-gutter-y-sm">
                  <q-input required filled square label="Label" v-model="form.label"
                     :rules="[(val) => !!val || 'Field is required']"></q-input>
                  <q-input label="Start Date" filled v-model="form.start_date" readonly
                     :rules="[(val) => !!val || 'Field is required']">
                     <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-date v-model="form.start_date" mask="YYYY-MM-DD HH:mm">
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-date>
                           </q-popup-proxy>
                        </q-icon>
                        <q-icon name="access_time" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-time v-model="form.start_date" mask="YYYY-MM-DD HH:mm" format24h>
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-time>
                           </q-popup-proxy>
                        </q-icon>
                     </template>
                  </q-input>
                  <q-input label="End Date" filled v-model="form.end_date" readonly
                     :rules="[(val) => !!val || 'Field is required']">
                     <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-date v-model="form.end_date" mask="YYYY-MM-DD HH:mm">
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-date>
                           </q-popup-proxy>
                        </q-icon>
                        <q-icon name="access_time" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-time v-model="form.end_date" mask="YYYY-MM-DD HH:mm" format24h>
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-time>
                           </q-popup-proxy>
                        </q-icon>
                     </template>
                  </q-input>
               </q-card-section>
               <q-card-actions class="justify-end q-pa-md sticky-bottom bg-grey-2">
                  <q-btn v-close-popup outline type="button" color="primary" label="Batal"></q-btn>
                  <q-btn :loading="loading" unelevated type="submit" color="primary" label="Simpan Data"></q-btn>
               </q-card-actions>
            </form>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
   name: "PromoIndex",
   data() {
      return {
         modal: false,
         form: {
            id: "",
            start_date: "",
            end_date: "",
            label: "",
         },
         selectedData: null,
         formType: "Tambah",
         search: "",
         productSearch: [],
         status: this.$route.query.status || "active",
         statuses: [
            { label: "Berjalan", value: "active" },
            { label: "Akan Datang", value: "later" },
            { label: "Kadaluarsa", value: "expired" },
         ],
      };
   },
   computed: {
      ...mapState({
         products: (state) => state.promo.products,
         promos: (state) => state.promo.promos,
      }),
      isDesktop() {
         return window.innerWidth > 600 ? true : false;
      },
      loading() {
         return this.$store.state.loading;
      },
   },
   watch: {
      status(newVal, oldVal) {
         if (oldVal != newVal) {
            this.$router.replace({ name: "PromoIndex", query: { status: newVal } });
            this.getData(newVal);
         }
      },
   },
   methods: {
      ...mapActions("promo", [
         "getPromos",
         "storePromo",
         "updatePromo",
         "deletePromo",
         "getProductPromo",
      ]),
      getData(val) {
          this.$store.commit('SET_LOADING', true)
           this.getPromos(val);
      },
      selectTab(val) {
         console.log(val);
      },
      handleAdd() {
         this.clearForm();
         this.formType = "Tambah";
         this.modal = true;
      },
      handleEdit(item) {
         this.clearForm();
         this.formType = "Edit";
         this.selectedData = item;
         this.setInputData();
         this.modal = true;
      },
      handleDelete(id) {
         this.$q
            .dialog({
               title: "Yakin akan menghapus ini?",
               message: "Data yang dihapus tidak dapat dikembalian",
               cancel: true,
            })
            .onOk(() => {
               this.deletePromo(id).then(() => {
                  this.$router.replace({
                     name: "PromoIndex",
                     query: { status: this.status },
                  });
                  this.getData(this.status);
               });
            });
      },
      setInputData() {
         this.form.id = this.selectedData.id;
         this.form.label = this.selectedData.label;
         this.form.start_date = this.selectedData.start_date;
         this.form.end_date = this.selectedData.end_date;
         this.form.discount_id = this.selectedData.discount_id;
      },
      submit() {
         let formdata = this.form;

         if (this.formType == "Edit") {
            formdata._method = "PUT";
            this.updatePromo(formdata).then((res) => {
               this.closeModal();
               this.status = res.data.data.status;
               this.$router.replace({
                  name: "PromoIndex",
                  query: { status: this.status },
               });
               this.getData(this.status);
            });
         }
         if (this.formType == "Tambah") {
            this.storePromo(formdata).then((res) => {
               this.closeModal();
               this.status = res.data.data.status;
               this.$router.replace({
                  name: "PromoIndex",
                  query: { status: this.status },
               });
               this.getData(this.status);
            });
         }
      },
      closeModal() {
         this.clearForm();
         this.modal = false;
      },
      clearForm() {
         this.form.id = "";
         this.form.label = "";
         this.form.start_date = "";
         this.form.end_date = "";
      },
   },
   created() {
      this.getData(this.status);
   },
    mounted() {
       this.$canAccess('view-product')
   }
};
</script>
