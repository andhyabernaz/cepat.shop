<template>
   <q-page padding>
      <AppHeader title="User List">
         <q-btn color="white" text-color="dark" icon="add" label="User" @click="handleAddUser" v-if="$can('create-user')"></q-btn>
      </AppHeader>
      <UserMenuTab></UserMenuTab>
      <q-card class="section shadow">
         <q-card-section>
            <div class="q-pb-md">
               <q-input v-model="queryParams.search" outlined dense @update:model-value="searchData" debounce="700"
                  placeholder="Ketik nama, email atau nomor telpon user" clearable @clear="reset">
               </q-input>
            </div>
            <div>
               <div class="table-responsive">
                  <table class="table aligned bordered">
                     <thead>
                        <tr>
                           <th v-for="h in ['#', 'User', 'Role', 'Email', 'Phone', 'Saldo', 'Aff Saldo' ,'Action']" :key="h">
                              {{ h }}
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr v-for="(user, i) in users.data" :key="i">
                           <td>{{ users.from + i }}</td>
                           <td top>
                              <div>{{ user.name }}</div>
                           </td>
                           <td>{{ user.role_name || '-' }}</td>
                           <td>
                              <div>{{ user.email }}</div>
                           </td>
                           <td>
                              <div>{{ user.phone }}</div>
                           </td>
                           <td>
                              {{ moneyFormat(user.saldo_balance) }}
                           </td>
                           <td>
                              {{ moneyFormat(user.affiliate_saldo) }}
                           </td>
                           <td class="flex no-wrap q-gutter-xs">
                              <!-- <q-btn size="11px" color="blue" round icon="list" @click="viewSaldo(user.id)">
                                 <q-tooltip>Riwayat Saldo</q-tooltip>
                              </q-btn> -->
                              <q-btn v-if="user.role_id != 1 && $can('delete-user')" size="11px" color="red" round icon="delete" @click="handleDelete(user)">
                              </q-btn>
                              <q-btn v-if="$can('update-user')" size="11px" color="blue" round icon="edit" @click="handleEdit(user)">
                              </q-btn>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div v-if="userNotAvailable" class="text-center q-py-md">
                  Tidak ada data
               </div>
               <div class="q-my-md">
                  <SimplePagination autoHide v-bind="users" @loadUrl="paginate"></SimplePagination>
               </div>
            </div>
         </q-card-section>
      </q-card>
      <q-dialog v-model="saldoModal">
         <div class="box-column" style="width:100%;max-width:800px">
            <div class="flex justify-between items-center q-mb-md">
               <div class="text-md">Riwayat Mutasi Saldo</div>
               <q-btn flat round v-close-popup icon="close"></q-btn>
            </div>
            <div class="scroll" style="max-height: 60vh;min-height: 200px;">
               <q-inner-loading :showing="saldoLoading"></q-inner-loading>
               <MutasisaldoTable :data="mutasi_saldos.data"></MutasisaldoTable>
            </div>
            <div>
               <SimplePagination v-bind="mutasi_saldos" @loadUrl="loadSaldo"></SimplePagination>
            </div>
         </div>
      </q-dialog>

      <q-dialog v-model="userModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-title flex-between">
                  <div>User Form</div>
                  <q-btn icon="close" flat round v-close-popup></q-btn>
               </div>
               <form @submit.prevent="submitUser" class="q-gutter-y-sm q-py-md" autocomplete="off">
                  <q-input v-model="userForm.name" color="grey-6" label="Nama *" dense lazy-rules autocomplete="off"
                     :rules="[val => val && val.length > 0 || 'Wajib diisi']">
                  </q-input>
                  <q-input v-model="userForm.email" color="grey-6" label="Email *" dense lazy-rules autocomplete="off"
                     :rules="[val => val && val.length > 0 || 'Please type email']">
                  </q-input>
                  <q-input v-model="userForm.phone" color="grey-6" label="No Ponsel / Whatsapp *" dense lazy-rules autocomplete="off"
                     mask="################" :rules="[validPhoneRules]">
                  </q-input>
                  <div class="q-pb-md">
                     <q-select :options="roleOptions" emit-value map-options v-model="userForm.role_id" :disable="editSelected && editSelected.role_id == 1"
                     label="Role"></q-select>
                  </div>
                  <q-btn class="q-mt-sm" v-if="!passwordInput" label="Update Password" @click="passwordInput = true"
                     flat padding="2px 8px" color="blue" no-caps size="12px"></q-btn>
                  <template v-if="passwordInput">
                     <q-input v-model="userForm.password" label="Password *" color="grey-6" autocomplete="new-password"
                        :type="isPwd ? 'password' : 'text'" dense :rules="[requiredRules]">
                        <template v-slot:append>
                           <q-icon :name="isPwd ? 'visibility' : 'visibility_off'" class="cursor-pointer"
                              @click="isPwd = !isPwd" />
                        </template>
                     </q-input>
                     <q-input v-model="userForm.password_confirmation" label="Konfirmasi Password" color="grey-6" autocomplete="off"
                        :type="isPwd ? 'password' : 'text'" dense :rules="[requiredRules]">
                        <template v-slot:append>
                           <q-icon :name="isPwd ? 'visibility' : 'visibility_off'" class="cursor-pointer"
                              @click="isPwd = !isPwd" />
                        </template>
                     </q-input>
                  </template>
                  <div class="q-pt-md">
                     <q-btn :loading="loading" type="submit" color="primary" class="full-width">Simpan Data</q-btn>

                  </div>
               </form>
            </q-card-section>
         </q-card>
      </q-dialog>

   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
import SimplePagination from "components/SimplePagination.vue";
import MutasisaldoTable from "components/MutasisaldoTable.vue";
import { moneyFormat, requiredRules } from "src/utils";
import UserMenuTab from "./UserMenuTab.vue";
export default {
   name: "UserList",
   components: { SimplePagination, MutasisaldoTable, UserMenuTab },
   data() {
      return {
         module: 'User',
         saldoLoading: false,
         saldoModal: false,
         resetPasswordModal: false,
         assignRoleModal: false,
         passwordInput: false,
         isPwd: true,
         mutasi_saldos: {
            data: [],
            total: 0,
            form: 1
         },
         roles: [],
         users: {
            data: [],
            total: 0,
            ready: false,
         },
         take: 10,
         canLoad: false,
         search: "",
         userNotAvailable: false,
         deleteId: null,
         load: false,
         userModal: false,
         queryParams: {
            search: "",
            per_page: 10,
         },
         editSelected: null,
         userForm: {
            name: '',
            email: '',
            phone: '',
            role_id: '',
            password: '',
            password_confirmation: '',
         }
      };
   },
   created() {
      if (!this.users.total) {
         this.getData();
      }
      this.getRoles()
   },
   mounted() {
       this.$canAccess('view-user')
   },
   computed: {
      isDesktop() {
         return window.innerWidth > 600 ? true : false;
      },
      loading() {
         return this.$store.state.loading
      },
      roleOptions() {
         return [
            { label: 'Customer', value: '' },
            ...this.roles.map(el => ({ label: el.name, value: el.id }))
         ]
      }
   },
   methods: {
      getData(url = null) {
         this.userModal = false
         if (!url) {
            url = `userList?${new URLSearchParams(this.queryParams).toString()}`;
         }

         this.$store.commit('SET_LOADING', true)

         BaseApi.get(url)
            .then((response) => {
               if (response.status == 200) {
                  this.users = { ...this.users, ...response.data.data };
                  this.users.ready = true;
               }
            })
      },
      getRoles() {
         BaseApi.get('roles').then(res => {
            this.roles = res.data.data
         })
      },
      paginate(url) {
         this.getData(url);
      },
      viewSaldo(user_id) {
         this.saldoModal = true
         this.loadSaldo(`mutasi-saldos?per_page=5&user_id=${user_id}`)
      },
      loadSaldo(url = null) {
         if (!url) {
            url = 'mutasi-saldos'
         }
         this.saldoLoading = true
         BaseApi.get(url).then(res => {
            this.mutasi_saldos = { ...res.data.data }
         }).finally(() => {
            this.saldoLoading = false
         })
      },
      searchData(val) {
         this.queryParams.search = val
         this.getData();
      },
      reset() {
         this.users = [];
         this.queryParams.search = "";
         this.getData();
      },
      handleDelete(user) {
         if (user.id == 1) {
            this.$q.notify({
               type: "negative",
               message: "User super admin tidak bisa di hapus",
            });
            return;
         }
         this.$q
            .dialog({
               title: "Yakin akan menghapus user?",
               message: "Data yang di hapus tidak dapat dikembalikan",
               cancel: true,
            })
            .onOk(() => {
               this.deleteUser(user.id);
            });
      },
      deleteUser(id) {
         this.$store.commit("SET_LOADING", true);
         BaseApi.delete("user/" + id).then((response) => {
            if (response.status == 200) {
               this.reset();
            }
         });
      },
      handleEdit(user) {
         this.clearUserForm()
         this.passwordInput = false
         this.editSelected = user
         this.userForm.name = user.name
         this.userForm.email = user.email
         this.userForm.phone = user.phone
         this.userForm.role_id = user.role_id
         this.userModal = true

      },
      handleAddUser() {
         this.clearUserForm()
         this.passwordInput = true
         this.editSelected = null
         this.userModal = true
      },
      clearUserForm() {
         this.userForm.name = ''
         this.userForm.email = ''
         this.userForm.phone = ''
         this.userForm.role_id = ''
         this.userForm.password = ''
         this.userForm.password_confirmation = ''
      },
      is_default(roles) {
         if (!roles.length) {
            return false
         }
         return roles.filter(r => r.is_default == true).length > 0
      },
      submitUser() {
         if(this.editSelected) {
            BaseApi.put(`users/${this.editSelected.id}`, this.userForm).then(() => {
               this.getData()
            })
         }else {
            BaseApi.post('users', this.userForm).then(() => {
               this.getData()
            })
         }
      },
   },
};
</script>