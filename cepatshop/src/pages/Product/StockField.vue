<template>
   <div class="stock-field q-gutter-y-xs">
      <div class="row items-center justify-between q-gutter-x-sm">
         <div class="text-caption text-grey-7">{{ label }}</div>
         <q-toggle
            :model-value="isUnlimited"
            label="Unlimited"
            color="primary"
            @update:model-value="handleModeChange"
         />
      </div>

      <q-slide-transition>
         <div v-show="!isUnlimited">
            <q-input
               :model-value="displayValue"
               type="number"
               inputmode="numeric"
               min="1"
               step="1"
               :outlined="outlined"
               :filled="filled"
               :stack-label="stackLabel"
               :error="hasError"
               error-message="Masukkan bilangan bulat positif"
               @update:model-value="handleInput"
               @keydown="preventInvalidInput"
            />
         </div>
      </q-slide-transition>

      <div v-if="isUnlimited" class="text-caption text-grey-6">
         Stok tidak terbatas.
      </div>
   </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
   modelValue: {
      type: [Number, String],
      default: null
   },
   label: {
      type: String,
      default: 'Stok'
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

const isUnlimited = computed(() => props.modelValue === null)
const displayValue = computed(() => isUnlimited.value ? '' : props.modelValue)
const hasError = computed(() => {
   if (isUnlimited.value) {
      return false
   }

   const parsed = Number.parseInt(props.modelValue, 10)
   return !Number.isInteger(parsed) || parsed < 1
})

function getFallbackStock() {
   const parsed = Number.parseInt(props.modelValue, 10)
   return Number.isInteger(parsed) && parsed > 0 ? parsed : 1
}

function handleModeChange(unlimited) {
   emit('update:modelValue', unlimited ? null : getFallbackStock())
}

function handleInput(value) {
   const digits = String(value ?? '').replace(/[^\d]/g, '')
   emit('update:modelValue', digits === '' ? 0 : Number.parseInt(digits, 10))
}

function preventInvalidInput(event) {
   if (['e', 'E', '+', '-', '.'].includes(event.key)) {
      event.preventDefault()
   }
}
</script>
