<script setup>
import { onMounted, ref } from 'vue';
import MenuTabs from './MenuTabs.vue'
import { BaseApi } from "boot/axios";
import { Dialog, Notify } from 'quasar';
import { useStore } from 'vuex';

const store = useStore()

const data = ref({ data: [], from: 1, total: 0 })

const statuses = [
   { label: 'All Message', value: '' },
   { label: 'Pending', value: 'Pending' },
   { label: 'Sent', value: 'Sent' },
   { label: 'Failed', value: 'Failed' },
]

const status = ref('')

const getData = (url = null) => {
   data.value.data = []
   store.commit('SET_LOADING', true)
   if (!url) {
      url = `messages?status=${status.value}`
      if (data.value.current_page) {
         url += `&page=${data.value.current_page}`
      }
   }
   BaseApi.get(url).then(res => {
      data.value = { ...res.data.data }
   })
}

onMounted(() => getData())

const errorLog = ref('')
const errorModal = ref(false)

const viewError = (log) => {
   errorLog.value = log
   errorModal.value = true
}

const sendNotification = (item) => {
   store.commit('SET_LOADING', true)
   BaseApi.post(`messages/${item.id}`).then((res) => {
      getData()
      Notify.create({
         type: 'positive',
         message: res.data.message
      })
   })
}
const handleSendNotification = (item) => {
   if (item.is_sent) {
      Dialog.create({
         title: 'Warning!',
         message: 'Notifikasi sudah terkirim, yakin akan mengirm ulang?',
         cancel: true,
         ok: { label: 'Kirim Ulang', flat: true }
      }).onOk(() => {
         sendNotification(item)
      })
   } else {
      sendNotification(item)
   }
}

const messageModal = ref(false)
const selected_message = ref('')
const viewMessage = (item) => {
   selected_message.value = item
   messageModal.value = true
}

</script>

<template>
   <q-page padding>
      <AppHeader title="Notifications">
      </AppHeader>
      <MenuTabs></MenuTabs>
      <q-card class="section shadow">
         <q-card-section>
            <q-select dense filled label="Status" class="q-mb-sm" :options="statuses" emit-value map-options
               v-model="status" @update:modelValue="getData()"></q-select>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['#', 'CreatedAt', 'Via', 'Recipient', 'Subject', 'Status', 'Error', 'Action']"
                           :key="h">{{ h
                           }}</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, idx) in data.data" :key="idx">
                        <td>{{ data.from + idx }}</td>
                        <td>{{ dateFormat(item.created_at, { hour: 'numeric', minute: 'numeric' }) }}</td>
                        <td>{{ item.via }}</td>
                        <td>{{ item.recipient }}</td>
                        <td>{{ item.subject }}</td>
                        <td>
                           <q-badge :color="getOrderStatusColor(item.status)">{{ item.status }}</q-badge>
                        </td>
                        <td>
                           <q-badge v-if="item.error_log" @click="viewError(item.error_log)" color="red" outline
                              class="cursor-pointer">View
                              Error</q-badge>
                           <div v-else>-</div>
                        </td>
                        <td class="q-gutter-sm">
                           <q-btn @click="viewMessage(item)" label="View" unelevated color="teal"
                              class="btn-action"></q-btn>
                           <q-btn v-if="$can('send-notification')" @click="handleSendNotification(item)" label="Send" unelevated
                              :color="item.is_sent ? 'grey-6' : 'primary'" class="btn-action"></q-btn>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <div class="text-center q-py-md" v-if="!data.total">Tidak ada data</div>
            </div>
            <SimplePagination v-bind="data" @loadUrl="getData" class="q-mt-sm"></SimplePagination>
         </q-card-section>
      </q-card>
      <q-dialog v-model="errorModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-subtitle flex">
                  <div>Error Log</div>
                  <q-space></q-space>
                  <q-btn round flat dense icon="close" v-close-popup></q-btn>
               </div>
               <div class="scroll bg-grey-10 text-grey-2" style="height:60vh">
                  <pre>
                     {{ errorLog }}
                  </pre>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
      <q-dialog v-model="messageModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-subtitle flex items-center">
                  <div>{{ selected_message.subject }}</div>
                  <q-space></q-space>
                  <q-btn round flat dense icon="close" v-close-popup></q-btn>
               </div>
               <div class="scroll bg-grey-2 q-pa-md" style="height:300px;">
                  <div v-html="selected_message.body" class="preline"></div>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>
