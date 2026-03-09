<template>
   <div class="promo-container">
      <div class="promo-heading">
         <div class="promo-title">
            <q-icon class="promo-icon" name="local_offer"></q-icon>
            <div class="promo-label">{{ promo.label }}</div>
         </div>
         <div class="promo-countdown">
            <div class="countdown-item">
               <div class="countdown-item-value">{{ dayEl }}</div>
               <div class="countdown-item-label">Hari</div>
            </div>
            <div class="countdown-sparator">:</div>
            <div class="countdown-item">
               <div class="countdown-item-value">{{ hourEl }}</div>
               <div class="countdown-item-label">Jam</div>
            </div>
            <div class="countdown-sparator">:</div>
            <div class="countdown-item">
               <div class="countdown-item-value">{{ minuteEl }}</div>
               <div class="countdown-item-label">Menit</div>
            </div>
            <div class="countdown-sparator">:</div>
            <div class="countdown-item">
               <div class="countdown-item-value">{{ secondEl }}</div>
               <div class="countdown-item-label">Detik</div>
            </div>
         </div>
      </div>
      <CarouselContainer :products="promo.products" ready />
   </div>
</template>

<script>
import CarouselContainer from 'components/CarouselContainer.vue'
export default {
   name: 'GlideProduct',
   props: {
      promo: Object
   },
   components: { CarouselContainer },
   data() {
      return {
         dayEl: 0,
         hourEl: 0,
         minuteEl: 0,
         secondEl: 0,
         countDownDate: null,
         interval: null,
      }
   },
   mounted() {
      if (this.promo) {
         this.startCoundown()
      }
   },
   methods: {
      startCoundown() {

         clearInterval(this.interval)

         this.countDownDate = new Date(this.promo.end_date).getTime();

         if (this.countDownDate) {

            this.interval = setInterval(this.moveTimer, 1000);

            this.moveTimer();
         } else {
            this.setTimer(0, 0, 0, 0);
         }
      },
      setTimer(days, hours, minutes, seconds) {
         this.dayEl = days < 10 ? '0' + days : days;
         this.hourEl = hours < 10 ? '0' + hours : hours;
         this.minuteEl = minutes < 10 ? '0' + minutes : minutes;
         this.secondEl = seconds < 10 ? '0' + seconds : seconds;
      },
      moveTimer() {
         var now = new Date().getTime();
         var distance = this.countDownDate - now;
         var days = Math.floor(distance / 86400000);
         var hours = Math.floor((distance % 86400000) / 3600000);
         var minutes = Math.floor((distance % 3600000) / 60000);
         var seconds = Math.floor((distance % 60000) / 1000);

         if (distance < 30000) {
            clearInterval(this.interval);
            this.setTimer(0, 0, 0, 0);
            this.$store.dispatch('flushData')
            this.$store.commit('front/REMOVE_PROMO', this.promo.id)
         } else {
            this.setTimer(days, hours, minutes, seconds);
         }

      }
   },
   beforeUnmount() {
      clearInterval(this.interval)
   }
}
</script>
