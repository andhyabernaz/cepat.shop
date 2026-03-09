import { boot } from 'quasar/wrappers'
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';

export default boot(async ({ app }) => {
  app.component('SwiperContainer', Swiper)
  app.component('SwiperSlide', SwiperSlide)
})
