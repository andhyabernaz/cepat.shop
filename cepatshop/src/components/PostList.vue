<template>
  <q-list class="relative bg-white">
    <q-item class="q-pa-md" clickable :to="{name: 'FrontPostShow', params:{slug: slug}}" @click="$emit('onClick', slug)">
      <q-item-section class="column justify-between">
        <div class="text-caption text-grey-7">{{ tags ? `In ${tags}` : '' }}</div>
      <div>
          <h3 class="text-weight-medium q-mb-xs text-md">{{ title }}</h3>
        <div class="post-list-teaser" v-if="with_teaser">{{ teaser }}</div>
      </div>
        <div class="text-caption text-grey-7">{{ created_locale }}</div>
      </q-item-section>
      <q-item-section avatar  v-if="asset" > 
          <img :src="asset.src" class="image-post-list thumbnail cursor-pointer rounded-borders" :alt="title"/>
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
    teaser: String,
    with_teaser: Boolean
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