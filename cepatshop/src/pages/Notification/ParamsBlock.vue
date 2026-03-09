<script>
import { copyToClipboard } from "quasar";
export default {
   props: ['params'],
   methods: {
      formatTitle(item) {
         let str = item.replace(/[^\w ]/, "");

         return str.split("_").join(" ");
      },
      formatItem(item) {
         return `{{ ${item} }}`;
      },
      handleCopyParam(item) {
         copyToClipboard(`{{ ${item} }}`).then(() => {
            this.$q.notify({
               type: "positive",
               message: `Coppied ${item.split("_").join(" ")} parameter`,
               timeout: 5,
            });
         });
      },
      handleInsert(item) {
         this.$emit('onInsert', ` {{ ${item} }} `)
      }
   }
}

</script>

<template>
   <div>
      <div class="scroll q-pb-md" style="max-height:74vh">
         <div v-for="(param, idx) in params" :key="idx">
            <q-list separator dense>
               <q-item-label header>{{ param.label }}</q-item-label>
               <q-item v-for="item in param.items" :key="item">
                  <q-item-section>
                     <q-item-label class="text-capitalize text-nowrap">
                        {{ formatTitle(item) }}
                     </q-item-label>
                  </q-item-section>
                  <q-item-section>
                     <q-item-label class="text-nowrap text-grey-8 text-xs">
                        {{ formatItem(item) }}
                     </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <q-btn label="insert" @click="handleInsert(item)" outline padding="2px 8px" no-caps color="teal"
                        size="12px"></q-btn>
                  </q-item-section>
               </q-item>
            </q-list>
         </div>
      </div>
      <!-- <q-expansion-item :label="param.label" header-class="text-weight-bold" v-for="(param, idx) in params" :key="idx"
         default-opened group="param">
      </q-expansion-item> -->
   </div>
</template>