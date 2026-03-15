<template>
   <div class="pagination flex items-center q-gutter-x-sm justify-center q-py-md" v-if="total > 0">
      <q-btn padding="4px 16px" size="12px" no-caps unelevated :disable="!prev_page_url"
         :color="prev_page_url ? 'primary' : 'grey-5'"
         @click="loadData(current_page - 1, prev_page_url)">Sebelumnya</q-btn>
      <q-btn padding="4px 16px" size="12px" no-caps unelevated :disable="!next_page_url"
         :color="next_page_url ? 'primary' : 'grey-5'"
         @click="loadData(current_page + 1, next_page_url)">Selanjutnya</q-btn>
   </div>
</template>

<script>
export default {
   props: {
      per_page: {
         default: 0,
      },
      current_page: {
         default: null,
      },
      last_page: {
         default: null,
      },
      total_page: {
         default: null,
      },
      prev_page_url: {
         default: null,
      },
      next_page_url: {
         default: null,
      },
      total: {
         default: 0,
      },
      justify: {
         type: String,
         default: "end",
      },
   },
   methods: {
      loadData(page, url) {
         this.$emit('loadmore', page)
         this.$emit('loadUrl', url)
      }
   }
};
</script>
