<script setup>
import { ref } from 'vue';

defineProps(['reviews'])
const previewImage = ref(null)
const previewModal = ref(false)
const handlePreviewImage = (src) => {
    previewImage.value = src
    previewModal.value = true
}
</script>

<template>
    <div class="block-review">
        <q-list>
            <q-item v-for="review in reviews" :key="review.id" class="q-px-none">
                <q-item-section avatar>
                    <q-avatar icon="eva-person-outline" color="grey-3" text-color="grey-7" class="q-mr-xs"></q-avatar>
                </q-item-section>
                <q-item-section>
                    <div class="q-px-xs">

                        <q-item-label>{{ review.name }}</q-item-label>
                        <q-item-label v-if="review.product_varian">{{ review.product_varian }}</q-item-label>
                    </div>
                    <div class="q-py-xs">

                        <q-rating data-nosnippet="true" readonly class="no-wrap" v-model="review.rating" color="accent"
                            icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half" size="1.2rem" />
                    </div>
                    <div class="bg-grey-1 q-pa-sm">
                        <div class="text-grey-8">{{ review.comment }}</div>
                        <div class="q-pt-xs text-grey-7 text-xs">{{ review.created }}</div>
                    </div>
                    <div class="q-mt-sm reviews-image-thumbs">
                        <q-img @click="handlePreviewImage(img.src)" class="img-thumbnail"
                            v-for="(img, i) in review.review_images" :key="i" :ratio="1" :src="img.src" width="50px"></q-img>
                    </div>
                    <div v-if="!review.is_approved" class="q-pa-xs text-xs text-orange">( Menunggu Moderasi )</div>
                </q-item-section>
            </q-item>
        </q-list>
    </div>
    <q-dialog v-model="previewModal">
        <img :src="previewImage" alt="">
    </q-dialog>
</template>