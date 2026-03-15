<template>
   <q-page padding>
      <AppHeader title="Affiliates Users"></AppHeader>
      <AffiliateTabMenu></AffiliateTabMenu>
      <div class="q-mt-md box-column flat">
         <div>
            <div class="card-subtitle row justify-between">
               <div>List Users</div>
               <div class="row q-gutter-x-sm">
                  <q-select style="min-width:150px" filled square dense label="Status" v-model="queryParam.status"
                     :options="options" map-options emit-value @update:model-value="loadData()"></q-select>
               </div>
            </div>
            <div class="table-responsive">
               <table class="table bordered middle aligned">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>CreatedAt</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, index) in users.data" :key="index">
                        <td>{{ users.from + index }}</td>
                        <td>{{ item.user_name }}</td>
                        <td>
                           <q-badge :color="item.status_color">
                              {{ item.status_label }}
                           </q-badge>

                        </td>
                        <td class="text-nowrap">{{ dateFormat(item.created_at) }}</td>
                        <td>
                           <q-btn-dropdown v-if="$can('manage-affiliate')" auto-close label="Action" color="teal" padding="3px 8px" class="btn-action" no-caps>
                              <q-list separator>
                                 <q-item clickable @click="handleSuspendAccount(item)" v-if="item.is_active || item.is_inactive">
                                    <q-item-section>Suspended Account</q-item-section>
                                 </q-item>
                                 <q-item clickable @click="handleActivatedAccount(item)" v-if="item.is_inactive || item.is_suspended">
                                    <q-item-section>Activated Account</q-item-section>
                                 </q-item>
                              </q-list>
                           </q-btn-dropdown>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <div class="text-center q-py-md" v-if="!users.total">Tidak ada data</div>
            </div>
            <SimplePagination v-bind="users" @loadUrl="getData"></SimplePagination>
         </div>
      </div>
   </q-page>
</template>

<script>
import { BaseApi } from 'boot/axios'
import AffiliateTabMenu from './AffiliateTabMenu'
import { dateFormat } from 'src/utils';
export default {
   components: { AffiliateTabMenu },
   data() {
      return {
         users: {
            data: [],
            from: 1,
            total: 1,
         },
         queryParam: {
            status: 'ALL',
         },
         options: [
            { label: 'Semua', value: 'ALL' },
            { label: 'Aktif', value: '1' },
            { label: 'Inactive', value: '2' },
            { label: 'SUspended', value: '3' },
         ]
      }
   },
   created() {
      this.getData()
   },
   mounted() {
       this.$canAccess('view-affiliate')
   },
   methods: {
      getLeadColor(status) {
         if (status == 'COMPLETED') {
            return 'green'
         }
         if (status == 'PROCESSED') {
            return 'teal'
         }
         return 'grey-8'
      },
      handleSuspendAccount(item) {
         BaseApi.put(`affiliates/${item.id}/updateStatus`, {
            status: 3
         }).then(() => {
            this.getData()
         })
      },
      handleActivatedAccount(item) {
         BaseApi.put(`affiliates/${item.id}/updateStatus`, {
            status: 1
         }).then(() => {
            this.getData()
         })
      },
      getData(url = null) {
         if (!url) {
            url = `affiliate-users?${new URLSearchParams(this.queryParam).toString()}`
         }
         this.$store.commit('SET_LOADING', true)
         BaseApi.get(url).then(res => {
            this.users = { ...res.data.data }
         }).finally(() => this.$store.commit('SET_LOADING', false))
      },
      loadData(url = null) {
         setTimeout(() => {
            this.getData(url)
         }, 100)
      }
   }
}
</script>