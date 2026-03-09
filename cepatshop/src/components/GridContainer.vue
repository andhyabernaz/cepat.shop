<template>
   <div class="grid-container" ref="container">
      <q-resize-observer @resize="onResize" />
      <div class="grid-items" ref="carousel" @mousedown="handleMouseDown" @mouseleave="handleMouseLeave"
         @mouseup="handleMouseUp" @mousemove="handleMouseMove">
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
         default: 10
      },
      spaceAfter: {
         default: 0
      },
      rows: {
         defult: 1
      }
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
         containerWidth: window.innerWidth,
         container: null,
         items: [],
         cwidth: window.innerWidth
      }
   },
   computed: {
      is_grab() {
         return this.movementX != 0
      },
   },
   mounted() {

      this.$nextTick(() => {
         this.carousel = this.$refs.carousel
         this.container = this.$refs.container;

         setTimeout(() => {
            this.setContent()
         }, 100)
      })

   },
   watch: {
      containerWidth(val) {

         if (!this.carousel) return

         this.setContent()
      }
   },
   methods: {
      onResize(ev) {
         this.containerWidth = ev.width
      },
      setContent() {
         if (!this.$refs.container) return

         this.cwidth = this.containerWidth

         let items = this.carousel.children

         let perView = items.length

         if (this.cwidth > 1024) {
            perView = 7
         } else if (this.cwidth <= 1024 && this.cwidth > 800) {
            perView = 6
         } else if (this.cwidth <= 800 && this.cwidth > 700) {
            perView = 5
         } else {
            perView = 4
         }

         this.cwidth -= ((perView - 1) * this.gap)

         let cardWith = this.cwidth / perView

         let grid = this.container.querySelector('.grid-items')

         let maxCardPerpage = parseInt(this.cwidth / cardWith)

         let itemsLength = items.length

         if (itemsLength >= (maxCardPerpage * 2)) {
            grid.style.gridAutoFlow = 'column'
         }

         if (itemsLength < (maxCardPerpage * 2)) {
            grid.style.display = 'flex'
            grid.style.flexWrap = 'nowrap'
         }
         grid.style.gridGap = `${this.gap}px`

         Array.from(items).forEach(el => {
            el.classList.add('carousel-item')
            el.style.width = `${cardWith}px`
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
