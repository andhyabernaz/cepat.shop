<template>
   <div class="q-my-md bg-white">
      <q-tabs align="left">
         <q-route-tab v-if="$can('view-user')" :to="{ name: 'UserList' }">Users</q-route-tab>
         <q-route-tab  v-if="$can('view-role')"  :to="{ name: 'RoleIndex' }">Roles</q-route-tab>
         <q-route-tab v-if="$can('view-permission')" :to="{ name: 'RolePermissions' }">Role Permissions</q-route-tab>
      </q-tabs>
   </div>
</template>

<script setup>
</script>