<script>
import UserMenuTab from './UserMenuTab.vue'
import { BaseApi } from 'boot/axios'
export default {
   components: { UserMenuTab },
   data() {
      return {
         form: {
            id: '',
            name: '',
            sort: 1
         },
         modalForm: false,
         is_strict: ['Role', 'Permission'],
         filter: {
            name: 'all',
            desc: 'All Permissions Access'
         },
         debounce: null,
         roles: [],
         modules: [],
         permissions: [],
      }
   },
   created() {
      this.getData()
   },
   mounted() {
      this.$canAccess('view-permission')
   },
   methods: {
      getData() {
         BaseApi.get('roles-permissions').then(res => {
            if (res.status == 200) {
               this.roles = res.data.data.roles
               this.permissions = res.data.data.permissions
               this.modules = res.data.data.modules

               if (!this.filter.name) {
                  this.filter = this.modules[0]
               }
            }
         })
      },
      getUserPermissions() {
         clearTimeout(this.debounce)

         this.debounce = setTimeout(() => {
            this.$store.dispatch('user/getUserPermissions')
         }, 1000)
      },
      handleToggle(permission_id, role_id) {
         const fd = {
            permission_id: permission_id,
            role_id: role_id
         }

         BaseApi.post('permissions/toggle', fd).then(() => {
            this.getData()
            this.getUserPermissions()
         })
      },
      isDisabled(role) {
         if (role.id == 1) {
            return true
         }
         if (!this.$can('manage-permission')) {

            if (role.id == 1) {
               return false
            }
            return true
         }
         return false
      },
      selectModule(module) {
         this.filter = { ...module }
      }
   },
   computed: {
      loading() {
         return this.$store.state.loading
      },
      filtered_data() {
         if (this.filter.name != 'all') {
            return this.permissions.filter(el => el.module == this.filter.name)
         }

         return this.permissions
      },
   }
}
</script>

<template>
   <q-page padding>
      <AppHeader title="Assign Permissions"></AppHeader>
      <UserMenuTab />

      <q-card class="section q-mt-md" flat>
         <q-card-section>
            <div class="q-mb-md">
               <div class="q-gutter-sm">
                  <q-btn unelevated size="13px" :color="filter.name == 'all' ? 'blue' : 'grey-6'"
                     @click="selectModule({ name: 'all', desc: 'All Permissions Access' })">ALL</q-btn>
                  <q-btn v-for="(module, idz) in modules" :key="idz" unelevated size="13px"
                     :color="filter && filter.name == module.name ? 'blue' : 'grey-6'" @click="selectModule(module)">{{
                        module.name }}</q-btn>

               </div>
               <div class="q-mt-md bg-green-1 text-green-9 q-pa-sm">{{ filter.desc }}</div>
            </div>
            <div class="table-responsive">
               <table class="table bordered">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Permissions</th>
                        <th v-for="item in roles" :key="item.id">{{ item.name }}</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, index) in filtered_data" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>
                           <div class="nowrap">{{ item.ability }}</div>
                        </td>
                        <td v-for="(role, roleIndex) in roles" :key="roleIndex">
                           <q-toggle :disable="isDisabled(role)" v-model="roles[roleIndex].permissions"
                              :color="isDisabled(role) ? 'grey-6' : ''" :val="item.id"
                              @update:model-value="() => handleToggle(item.id, role.id)"></q-toggle>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </q-card-section>
      </q-card>

   </q-page>
</template>