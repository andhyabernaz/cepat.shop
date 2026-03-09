<template>
   <q-page padding>
      <AppHeader title="Roles">
         <q-btn icon="add" label="Role" color="white" text-color="dark" @click="handleAdd" v-if="$can('create-role')"></q-btn>
      </AppHeader>
      <RoleMenuTab />
      <q-card class="section shadow">
         <q-card-section>
            <div class="">
               <q-list separator>
                  <q-item class="item-header">
                     <q-item-section avatar>#</q-item-section>
                     <q-item-section>Role Name</q-item-section>
                     <q-item-section side
                        v-if="$can('delete-role') || $can('update-role')">Action</q-item-section>
                  </q-item>
                  <q-item v-for="(item, index) in roles" :key="index">
                     <q-item-section avatar>{{ index + 1 }}</q-item-section>
                     <q-item-section>{{ item.name }}</q-item-section>
                     <q-item-section side>
                        <div class="q-gutter-xs">
                           <q-btn v-if="$can('delete-role')" :disable="item.is_default == 1" color="red" round size="sm"
                              icon="delete" @click="handleDelete(item)"></q-btn>
                           <q-btn v-if="$can('update-role')" :disable="item.is_default == 1" color="blue" round size="sm"
                              icon="edit" @click="handleEdit(item)"></q-btn>
                        </div>
                     </q-item-section>
                  </q-item>
                  <div class="text-center q-py-md" v-if="!loading && !roles.length">TIdak ada data</div>
               </q-list>
            </div>
         </q-card-section>
      </q-card>
      <q-dialog v-model="formModal">
         <q-card class="card-md">
            <q-card-section>
               <div class="card-title">Role Form</div>
               <form @submit.prevent="submitData">
                  <q-input label="Role Name" v-model="form.name" required></q-input>
                  <div class="card-action">
                     <q-btn :loading="loading" label="Submit" color="primary" type="submit"></q-btn>
                     <q-btn :disable="loading" label="Cancel" color="primary" outline v-close-popup></q-btn>
                  </div>
               </form>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { BaseApi } from 'src/boot/axios';
import RoleMenuTab from './UserMenuTab.vue'
export default {
   components: { RoleMenuTab },
   data() {
      return {
         module: 'Role',
         formModal: false,
         roles: [],
         selected: null,
         form: {
            _method: 'POST',
            id: '',
            name: '',
         }
      }
   },
   computed: {
      loading() {
         return this.$store.state.loading
      }
   },
   methods: {
      getData() {
         BaseApi.get('roles').then(res => {
            this.roles = res.data.data
         })
      },
      clearForm() {
         this.form.id = ''
         this.form.name = ''
      },
      handleAdd() {
         this.clearForm()
         this.selected = null
         this.formModal = true
      },
      handleEdit(role) {
         this.clearForm()
         this.selected = role
         this.form.name = role.name
         this.formModal = true
      },
      handleDelete(role) {
         this.$q.dialog({
            title: 'Konfirmasi Penghapusan',
            message: 'Menghapus role akan berpengaruh terhadap user akses',
            cancel: true
         }).onOk(() => {
            this.$store.commit('SET_LOADING', true)
            BaseApi.delete(`roles/${role.id}`).then(() => {
               this.getData()
            })
         })
      },
      submitData() {
         this.$store.commit('SET_LOADING', true)

         if (this.selected) {
            BaseApi.put(`roles/${this.selected.id}`, this.form).then(() => {
               this.getData()
            })
         } else {
            BaseApi.post('roles', this.form).then(() => {
               this.getData()
            })

         }

         this.formModal = false
      }
   },
   created() {
      this.getData()
   },
    mounted() {
       this.$canAccess('view-role')
   },
}
</script>
