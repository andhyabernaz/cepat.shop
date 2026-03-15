<script>

export default {
   props: {
      title: {
         default: 'Kembali'
      },
      text: {
         default: 'NO ITEMS FOUND'
      },
      routeName: String
   }
}

</script>

<template>
   <div class="column items-center text-grey-7 q-pa-md">
      <img class="empty-data-img" src="static/empty-data.png" />
      <p class="text-center text-md q-mt-md">{{ text }}</p>
      <q-btn class="q-mt-md" icon-right="eva-arrowhead-right" :to="{ name: routeName }" color="primary" :label="title" />
   </div>
</template>
