<script setup>
import { Loading, Notify } from 'quasar';
import { computed, ref } from 'vue';
import { Api } from 'boot/axios'
import { useStore } from 'vuex';

const props = defineProps(['invoice'])
const store = useStore()

const emit = defineEmits(['onSuccess'])

const modal = ref(false)
const form = ref(null)

const preview = computed(() => {
   if (form.value) {
      return URL.createObjectURL(form.value)
   }
   return ''
})
const handleUploadBuktiTransfer = () => {
   const input = document.getElementById('inputFile')
   input.value = ''
   input.click()

}

const inputChange = (e) => {
   form.value = e.target.files[0]
   modal.value = true
}
const uploadImage = () => {
   modal.value = false
   let formData = new FormData();
   formData.append('image', form.value);

   Loading.show();

   Api.post('uploadPaymentProof/' + props.invoice.id,
      formData,
      {
         headers: { 'Content-Type': 'multipart/form-data' }
      }).then((res) => {

         store.commit('order/SET_INVOICE', res.data.data)

         Notify.create({
            type: 'positive',
            message: 'Berhasil upload bukti transfer'
         })
         emit('onSuccess')
      })
      .finally(() => {
         Loading.hide()
      })
}

const buktiModal = ref(false)
</script>

<template>
   <div>
      <q-card class="section shadow q-mt-md" v-if="invoice.transaction.payment_proof">
         <q-card-section>
            <div>
               <!-- <q-btn @click.prevent="handleUploadBuktiTransfer" v-if="!invoice.transaction.payment_proof" unelevated
                  no-caps label="Upload Bukti Transfer" class="full-width" color="primary" text-color="white"
                  style="border-radius:none;"></q-btn> -->
               <div v-if="invoice.transaction.payment_proof">
                  <div class="card-subtitle">Bukti Pembayaran</div>
                  <img :src="invoice.transaction.payment_proof_url"
                     style="width:110px;height:110px;object-fit:cover;object-position: top"
                     class="cursor-pointer thumbnail" @click="buktiModal = true" />
               </div>
               <q-dialog v-model="modal">
                  <q-card class="card-md relative">
                     <q-card-section class="scroll" style="max-height:70vh">
                        <img :src="preview" style="width:100%" />

                     </q-card-section>
                     <q-btn style="position:absolute;top:0;right:0" icon="close" color="red" v-close-popup
                        round></q-btn>
                     <q-card-actions>
                        <q-btn label="Upload" class="full-width" color="blue" @click="uploadImage"></q-btn>
                     </q-card-actions>
                  </q-card>
               </q-dialog>
               <input type="file" accept="image/*" class="hidden" id="inputFile" @change="inputChange">
            </div>
         </q-card-section>
      </q-card>

      <q-dialog v-model="buktiModal">
         <img :src="invoice.transaction.payment_proof_url" class="payment-proof" />
      </q-dialog>
   </div>
</template>