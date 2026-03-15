<template>
   <q-input :label="label" :prefix="prefix" :suffix="suffix" :required="required" :filled="filled" :debounce="20"
      :outlined="outlined" v-model="getModelValue" :stackLabel="stackLabel"></q-input>
</template>

<script setup>
import { moneyFormat } from 'src/utils'
import { computed } from 'vue'
const props = defineProps({
   modelValue: {
      type: [String, Number],
      default: ''
   },
   label: {
      type: String,
      default: 'Harga'
   },
   prefix: {
      type: String,
      default: ''
   },
   suffix: {
      type: String,
      default: ''
   },
   required: {
      type: Boolean,
      default: false
   },
   stackLabel: {
      type: Boolean,
      default: false
   },
   filled: {
      type: Boolean,
      default: false
   },
   outlined: {
      type: Boolean,
      default: false
   }
})

const emit = defineEmits(['update:modelValue'])

function cleaning(val) {
   return val.replace(/\D/g, '')
}

const getModelValue = computed({
   get() {
      return moneyFormat(props.modelValue)
   },
   set(val) {
      let amount = val ? cleaning(val) : '0'
      emit('update:modelValue', Number(amount))
   }
})

</script>