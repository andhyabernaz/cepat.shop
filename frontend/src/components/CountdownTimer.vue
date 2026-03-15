<script setup>
import { computed } from "vue";
import { onBeforeUnmount } from "vue";
import { onMounted, reactive, ref } from "vue";

const props = defineProps({
   date: {
      required: true,
   },
   title: String,
   background: {
      default: "primary",
   },
   color: {
      default: "white",
   },
   captionColor: String,
   padding: {
      type: String,
      default: "12px",
   },
});

const countdown = reactive({
   days: "0",
   hours: "0",
   minutes: "0",
   seconds: "0",
   has_expired: false,
});

const countdown_interval = ref(null);
const countdown_date = ref(null);

const moveTimer = function () {
   countdown.has_expired = false;
   let now = new Date().getTime();
   let distance = countdown_date.value - now;

   if (distance > 0) {
      let days = Math.floor(distance / 86400000);
      let hours = Math.floor((distance % 86400000) / 3600000);
      let minutes = Math.floor((distance % 3600000) / 60000);
      let seconds = Math.floor((distance % 60000) / 1000);

      countdown.days = days;
      countdown.hours = hours < 10 ? "0" + hours : hours;
      countdown.minutes = minutes < 10 ? "0" + minutes : minutes;
      countdown.seconds = seconds < 10 ? "0" + seconds : seconds;
   } else {
      clearInterval(countdown_interval.value);
      setTimeout(() => {
         countdown.has_expired = true;
      }, 100)
   }
};

const startCountdown = () => {
   countdown_date.value = new Date(props.date).getTime();

   if (countdown_date.value) {
      moveTimer();
      countdown_interval.value = setInterval(moveTimer, 1000);
   } else {
      countdown.days = "0";
      countdown.hours = "0";
      countdown.minutes = "0";
      countdown.seconds = "0";
   }
};

onMounted(() => {
   setTimeout(() => {
      startCountdown();
   }, 500)
});

onBeforeUnmount(() => {
   clearInterval(countdown_interval.value);
});

const classes = computed(() => {
   return `bg-${props.background} text-${props.color}`;
});

const styles = computed(() => {
   return `padding: ${props.padding}`;
});
</script>

<template>
   <section>
      <div :class="classes" :style="styles" class="text-center">
         <div class="text-weight-bold text-sm" v-if="title">{{ title }}</div>
         <div class="flex text-lg q-gutter-x-xs justify-center text-center" v-if="countdown.has_expired == false">
            <div class="" v-if="countdown.days > 0">
               <div class="text-weight-bold line-height-normal">
                  {{ countdown.days }}
               </div>
               <div class="text-xs">Hari</div>
            </div>
            <div v-if="countdown.days > 0" class="text-weight-bold line-height-normal">:</div>
            <div>
               <div class="text-weight-bold line-height-normal">
                  {{ countdown.hours }}
               </div>
               <div class="text-xs">Jam</div>
            </div>
            <div class="text-weight-bold line-height-normal">:</div>
            <div>
               <div class="text-weight-bold line-height-normal">
                  {{ countdown.minutes }}
               </div>
               <div class="text-xs">Menit</div>
            </div>
            <div class="text-weight-bold line-height-normal">:</div>
            <div>
               <div class="text-weight-bold line-height-normal">
                  {{ countdown.seconds }}
               </div>
               <div class="text-xs">Detik</div>
            </div>
         </div>
         <div class="text-center text-lg" v-if="countdown.has_expired">
            Expired!
         </div>
      </div>
   </section>
</template>