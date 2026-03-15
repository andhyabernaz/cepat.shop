<script setup>

const icons = [
   'bank-bca.png',
   'bank-bni.png',
   'bank-bri.png',
   'bank-bsi.png',
   'bank-danamon.png',
   'bank-mandiri.png',
   'bank-permata.png',
   'bank-muamalat.png',
   'bank-sinarmas.png',
   'alfamart.png',
   'indomaret.png',
   'ovo.png',
   'qris.png',
   'shopeepay.png',
   'dana.png',
   'gopay.png',
]

</script>

<template>
  <div ref="container" class="payment-icon-groups">
     <img v-for="icon in icons" :key="icon" :src="`/payment-icons/${icon}`" />
   </div>
</template>