<template>
  <div class="overflow-hidden glide-product">
    <div class="row items-center q-gutter-x-sm q-py-sm">
      <div class="text-weight-bold text-lg text-secondary q-py-xs">{{ promo.label }}</div>
      <div class="row text-sm q-gutter-x-xs text-weight-bold">
        <div class="bg-dark text-white rounded-borders q-px-xs">{{ dayEl }}</div>
        <div class="bg-dark text-white rounded-borders q-px-xs">{{ hourEl }}</div>
        <div class="bg-dark text-white rounded-borders q-px-xs">{{ minuteEl }}</div>
        <div class="bg-dark text-white rounded-borders q-px-xs">
          <transition>
            <span>{{ secondEl }}</span>
          </transition>
        </div>
      </div>
    </div>
    <vue-glide :options="glideOptions" class="">
      <vue-glide-slide v-for="product in promo.products" :key="product.id" class="relative">
        <SwiperProductCard :product="product" />
      </vue-glide-slide>
    </vue-glide>
  </div>
</template>

<script>
import SwiperProductCard from 'components/SwiperProductCard.vue'
export default {
  name: 'GlideProduct',
  props: {
    promo: Object
  },
  components: { SwiperProductCard },
  data() {
    return {
      glideOptions: {
        dragThreshold: 20,
        swipeThreshold: 10,
        touchAngle: 30,
        rewind: false,
        perView: 2,
        gap: 8,
        bound: true,
        peek: {
          before: 0,
          after: 50
        },
        breakpoints: {
          3000: {
            perView: 3,
            peek: {
              before: 0,
              after: 50
            },
          },
          900: {
            perView: 2,
            peek: {
              before: 0,
              after: 30
            },
          },
          400: {
            perView: 2,
            peek: {
              before: 0,
              after: 30
            },
          },
        }
      },
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

      if (distance < 1) {
        clearInterval(this.interval);
        this.setTimer(0, 0, 0, 0);
        this.$store.dispatch('getInitialData', true)
      } else {

        this.setTimer(days, hours, minutes, seconds);
      }

    }
  },
  beforeDestroy() {
    clearInterval(this.interval)
  }
}
</script>
