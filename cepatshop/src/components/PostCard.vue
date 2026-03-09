<template>
   <q-card class="relative" flat bordered>
      <q-img :ratio="16 / 9" v-if="asset" :src="asset.src" class="cursor-pointer" :alt="title"
         @click="$router.push({ name: 'FrontPostShow', params: { slug: slug } })" />
      <q-card-section @click="$router.push({ name: 'FrontPostShow', params: { slug: slug } })">
         <div>
            <h2 class="ellipsis-2-lines text-weight-medium q-mb-xs" style="font-size:16px;">{{ title }}</h2>
            <div class="text-grey-7 text-sm teaser" v-html="teaser">
            </div>
            <div class="q-pt-sm flex justify-between" style="margin-top:auto;">
               <div class="text-amber-9 text-sm1" v-if="tags">{{ tags }}</div>
            </div>
         </div>

      </q-card-section>
   </q-card>
</template>
<script>
export default {
   name: 'PostList',
   props: {
      title: String,
      slug: String,
      image_url: String,
      tags: String,
      body: String,
      created_locale: String,
      asset: Object,
      teaser: String
   },
   methods: {
      getTeaser(html) {
         if (html) {
            let strippedString = html.replace(/(<([^>]+)>)/gi, "");
            return strippedString.substr(0, 230)
         } else {
            return ''
         }
      },
   }
}
</script>