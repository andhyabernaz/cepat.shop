<script setup>

import MediaComponent from 'components/MediaComponent.vue'

</script>

<template>
   <q-page class="q-pb-lg bg-grey-1" padding>
      <AppHeader title="File Media"></AppHeader>
      <MediaComponent deletable></MediaComponent>
   </q-page>
</template>