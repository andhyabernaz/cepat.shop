<template>
  <q-list class="relative bg-white border-b">
    <q-item class="q-pa-md" clickable :to="{name: 'FrontPostShow', params:{slug: slug}}" @click="$emit('onClick', slug)">
      <q-item-section class="column justify-between">
        <div class="text-caption text-grey-7" v-if="tags">In {{ tags }}</div>
       <div>
         <h3 class="text-weight-medium q-mb-xs text-md">{{ title }}</h3>
        <div class="post-list-teaser">{{ teaser }}</div>
       </div>
        <div class="text-caption text-grey-7">{{ created_locale }}</div>
      </q-item-section>
      <q-item-section avatar  v-if="asset" > 
          <img :src="asset.src" ratio="1" class="image-post-list cursor-pointer rounded-borders" :alt="title"/>
      </q-item-section>
     
    </q-item>
  </q-list>
</template>
<script>
export default {
  name: 'PostList',
  props:{
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
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 120)
      } else {
        return ''
      }
    },
  }
}
</script>