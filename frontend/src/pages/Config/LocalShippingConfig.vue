<template>
   <div>
      <PickupOrder></PickupOrder>
      <LocalShipping class="q-mt-md">
      </LocalShipping>
   </div>
</template>

<script>
import LocalShipping from './Components/LocalShipping.vue'
import PickupOrder from './Components/PickupOrder.vue'
export default {
   components: { LocalShipping, PickupOrder },
};
</script>
