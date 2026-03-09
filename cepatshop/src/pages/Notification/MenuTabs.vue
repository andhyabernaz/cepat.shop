<script setup>
const routes = [
   // { label: 'Notifications', route: 'NotificationAdmin' },
   { label: 'Notification Template', route: 'NotificationTemplate' },
   { label: 'All Messages', route: 'NotificationMessage' },
]

</script>

<template>
   <q-tabs align="left" class="bg-white q-mb-md" indicator-color="primary">
      <q-route-tab v-for="(item, idx) in routes" :key="idx" :to="{ name: item.route }"
         :label="item.label"></q-route-tab>
   </q-tabs>
</template>