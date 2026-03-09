<template>
   <q-page padding>
      <AppHeader title="Akun Pembayaran">
         <q-btn v-if="$can('create-payment-account')" color="white" text-color="dark" icon="add" @click="handleAdd" label="Akun Pembayaran" />
      </AppHeader>

      <q-card class="section shadow">
         <q-card-section>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['Vendor / Title', 'Nama Akun', 'Nomor Akun', 'Action']" :key="h">
                           {{ h }}
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="bank in banks.data" :key="bank.id">
                        <td>
                           {{ bank.bank_name }}
                        </td>
                        <td>{{ bank.account_name }}</td>
                        <td>{{ bank.account_number }}</td>
                        <td>
                           <div class="flex no-wrap q-gutter-xs">
                              <q-btn v-if="$can('delete-payment-account')" round @click="remove(bank.id)" size="11px" icon="delete" color="red" />
                              <q-btn v-if="$can('update-payment-account')" round @click="edit(bank)" size="11px" color="blue" icon="edit" />
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div v-if="!banks.available" class="text-center q-py-md">
               Tidak ada data
            </div>
         </q-card-section>
      </q-card>

      <q-dialog v-model="modal">
         <q-card style="width: 100%; max-width: 400px">
            <div class="card-heading text-md">
               {{ formType == "add" ? "Tambah" : "Edit" }} Data
            </div>
            <form @submit.prevent="submit">
               <q-card-section>
                  <q-input filled v-model="form.bank_name" label="Nama Vendor / Title" :rules="[requiredRules]"
                     placeholder="Gopay, OVO, BCA dll" />
                     <q-input filled v-model="form.account_number" label="Nomor Akun" :rules="[requiredRules]"
                     placeholder="6985XXXXXXXXXXXX" />
                     <q-input filled v-model="form.account_name" label="Nama Akun" :rules="[requiredRules]" />
               </q-card-section>
               <q-card-actions class="q-pa-md sticky-bottom bg-grey-2">
                  <q-btn :loading="loading" unelevated label="Simpan Data" type="submit" color="primary"></q-btn>
                  <q-btn :disable="loading" label="Batal" type="button" color="primary" outline
                     @click.prevent="closeModal"></q-btn>
               </q-card-actions>
            </form>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { requiredRules } from "src/utils";
import { mapState, mapActions } from "vuex";
export default {
   name: "BankIndex",
   data() {
      return {
         formType: "",
         modal: false,
         form: {
            id: "",
            _method: "POST",
            bank_name: "",
            bank_office: "",
            account_name: "",
            account_number: "",
         },
      };
   },
   computed: {
      ...mapState({
         banks: (state) => state.bank.banks,
      }),
      loading() {
         return this.$store.state.loading
      },
   },
   methods: {
      ...mapActions("bank", [
         "getBanks",
         "destroyBank",
         "updateBank",
         "storeBank",
      ]),
      submit() {
         if (this.loading) return;
         if (this.formType == "add") {
            this.storeBank(this.form);
            this.closeModal();
         }
         if (this.formType == "edit") {
            this.updateBank(this.form);
            this.closeModal();
         }
      },
      remove(id) {
         this.$q
            .dialog({
               title: "Konfirmasi Penghapusan Item",
               message: "Yakin akan menghapus data?",
               ok: { label: "Hapus", flat: true, "no-caps": true },
               cancel: { label: "Batal", flat: true, "no-caps": true },
            })
            .onOk(() => {
               this.destroyBank(id);
            });
      },
      handleAdd() {
         this.formType = "add";
         this.modal = true;
         this.form._method = "POST";
      },
      edit(bank) {
         this.setData(bank);
         this.formType = "edit";
         this.form._method = "PUT";
         this.modal = true;
      },
      setData(data) {
         this.form.bank_name = data.bank_name;
         this.form.bank_office = data.bank_office;
         this.form.account_name = data.account_name;
         this.form.account_number = data.account_number;
         this.form.id = data.id;
      },
      closeModal() {
         this.modal = false;
         this.clearData();
      },
      clearData() {
         this.form.bank_name = "";
         this.form.bank_office = "";
         this.form.account_name = "";
         this.form.account_number = "";
         this.form.id = "";
      },
      getData() {
         this.$store.commit('SET_LOADING', true)
         this.getBanks()
      }
   },
   created() {
      if (!this.banks.data.length) {
         this.getData()
      };
   },
};
</script>
