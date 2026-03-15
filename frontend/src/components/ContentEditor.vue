<template>
   <div>
      <q-editor v-model="inner_content" ref="editor" min-height="15rem" :definitions="{
         embed: {
            tip: 'Embed Video',
            icon: 'ion-logo-youtube',
            handler: handleEmbedModal
         },
      }" :toolbar="[
         ['left', 'center', 'right', 'justify', 'bold', 'italic', 'underline', 'strike', 'quote', 'unordered', 'ordered', 'outdent', 'indent', 'link', 'embed', 'removeFormat', 'subscript', 'superscript',
            {
               icon: $q.iconSet.editor.formatting,
               list: 'no-icons',
               options: [
                  'h1',
                  'h2',
                  'h3',
                  'h4',
                  'h5',
                  'h6',
                  'p',
                  'code'
               ]
            }, 'fullscreen', 'viewsource',
         ],
      ]" />
      <q-dialog v-model="embed_video.modal" persistent>
         <q-card class="card-lg">
            <div class="card-heading">
               <h4>Embed Video</h4>
            </div>
            <q-card-section>
               <div class="row q-mb-md">
                  <div class="col q-pr-xs">
                     <q-select filled label="Ratio" :options="embed_video.options" emit-value map-options
                        v-model="embed_video.ratio"></q-select>
                  </div>
                  <div class="col q-pl-xs">
                     <q-input filled label="Max Width" v-model="embed_video.max_width"></q-input>
                  </div>
               </div>
               <q-input filled type="textarea" label="Video" v-model="embed_video.source"
                  hint="Video source eg: <iframe src=''></iframe>"></q-input>
            </q-card-section>
            <q-card-section>
               <q-btn label="Insert" color="primary" unelevated class="q-mx-xs" @click="saveEmbed"></q-btn>
               <q-btn label="Cancel" color="primary" outline class="q-mx-xs" v-close-popup></q-btn>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
export default {
   props: ['modelValue'],
   data() {
      return {
         embed_video: {
            ratio: 'item-16by9',
            source: '',
            modal: false,
            max_width: '600px',
            options: [
               { label: 'Ratio 21:9', value: 'item-21by9' },
               { label: 'Ratio 16:9', value: 'item-16by9' },
               { label: 'Ratio 4:3', value: 'item-4by3' },
               { label: 'Ratio 1:1', value: 'item-1by1' },
            ],
         }
      }
   },
   computed: {
      inner_content: {
         get() {
            return this.modelValue
         },
         set(val) {
            this.$emit('update:modelValue', val)
         }
      }
   },
   methods: {
      handleEmbedModal() {
         this.embed_video.source = ''
         this.embed_video.modal = true
      },
      saveEmbed() {
         if (!this.embed_video.source) return

         this.embed_video.modal = false

         let editor = this.$refs.editor
         const html = `<br><div contenteditable="false" class="embed-responsive ${this.embed_video.ratio}" style="max-width:${this.embed_video.max_width};">${this.embed_video.source}</div><br><br>`

         this.$nextTick(function () {

            editor.caret.restore()

            editor.runCmd('insertHTML', html)

            editor.focus()
         })

      },
   }
}

</script>
