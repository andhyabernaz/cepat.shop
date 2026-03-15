<template>
   <div class="carousel-container" ref="container">
        <q-resize-observer @resize="onResize" />
      <div class="carousel-items" ref="carousel" @mousedown="handleMouseDown" @mouseleave="handleMouseLeave"
         @mouseup="handleMouseUp" @mousemove="handleMouseMove" :style="styles">
         <slot></slot>
      </div>

   </div>
</template>

<script>
export default {
   props: {
      perView: {
         default: 0
      },
      maxPerView: {
         default: 0
      },
      gap: {
         default: 8
      },
      spaceAfter: {
         default: 0
      },
      rows: {
         defult: 1
      },
      fluid: Boolean
   },
   data() {
      return {
         isGrab: false,
         carousel: null,
         isDown: false,
         startX: 0,
         scrollLeft: 0,
         movementX: 0,
         pageX: 0,
         container: null,
         items: [],
         containerWidth: window.innerWidth,
         cWidth: window.innerWidth
      }
   },
   computed: {
      is_grab() {
         return this.movementX != 0
      },
      styles() {
         return `gap:${this.gap}px`
      },
      page_width() {
         return this.$store.state.page_width
      }
   },
   watch: {
      cWidth(val) {
         if (!this.carousel) return
         this.setContent()
      }
   },
   mounted() {
      this.$nextTick(() => {
         this.carousel = this.$refs.carousel
      })

   },
   methods: {
      onResize(ev) {
         this.cWidth = ev.width
      },
      setContent() {
         if (!this.carousel) {
            this.carousel = this.$refs.carousel
         }

         let items = this.carousel.children
         this.containerWidth = this.cWidth

         let calcWidth = this.containerWidth - this.spaceAfter
         let perView = items.length
         if (this.perView > 0) {
            perView = this.perView
         }
         if (this.maxPerView > 0 && perView > this.maxPerView) {
            perView = this.maxPerView
         }

         if (this.fluid) {
            if (Array.from(items).length < perView) {
               perView = Array.from(items).length
            }
         }

         calcWidth -= (this.gap * perView)

         Array.from(items).forEach(el => {
            el.classList.add('carousel-item')
            el.style.width = `${calcWidth / perView}px`
         })
      },
      handleMouseDown(e) {
         this.isDown = true;

         // this.carousel.classList.add('active');

         this.startPageX = e.pageX
         this.startX = e.pageX - this.carousel.offsetLeft;

         this.scrollLeft = this.carousel.scrollLeft;
      },
      handleMouseLeave(e) {
         this.isDown = false;
         // this.carousel.classList.remove('active');
         this.movementX = 0
      },
      handleMouseUp(e) {
         this.isDown = false;
         // this.carousel.classList.remove('active');
         setTimeout(() => {
            this.movementX = 0
         }, 100);
      },
      handleMouseMove(e) {
         if (!this.isDown) return;

         this.movementX = e.movementX

         e.preventDefault();
         this.pageX = e.pageX - this.carousel.offsetLeft;
         const walk = (this.pageX - this.startX) * 1.2; //scroll-fast
         this.carousel.scrollLeft = this.scrollLeft - walk;
      },
   }
}
</script>
