<template>
   <q-page-sticky :offset="[10, 10]" class="back-to-top">
      <Transition name="slide-right">
         <q-btn v-show="showTotop" icon="eva-chevron-up" class="btt" round color="primary" glossy
            @click="jumpTo('q-app')"></q-btn>
      </Transition>
   </q-page-sticky>
</template>

<script>
export default {
   data() {
      return {
         showTotop: false
      }
   },
   methods: {
      setTotop() {
         if (window.scrollY > 1000) {

            this.showTotop = true
         } else {
            this.showTotop = false.v
         }
      }
   },
   created() {
      window.addEventListener('scroll', this.setTotop)
   },
   unmounted() {
      window.removeEventListener('scroll', this.setTotop)
   },
}
</script>

<style>
.slide-right-enter-active {
   animation: slide-left 0.5s ease-in-out;
}

.slide-right-leave-active {
   animation: slide-right 0.5s ease-in-out;
}

@keyframes slide-right {
   0% {
      transform: translateX(0);
   }

   100% {
      transform: translateX(500px);
   }
}

@keyframes slide-left {
   0% {
      transform: translateX(500px);
   }

   100% {
      transform: translateX(0);
   }
}
</style>