<template>
  <div ref="block" class="block-observe hidden" :class="classObserve">
    <slot></slot>
  </div>
</template>

<script>
export default {
  data() {
    return {
      classObserve: ''
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.intersecObserve()
    })
  },
  methods: {
    intersecObserve() {
      let el = this.$refs.block

      el.classList.remove('hidden')

      let opts = {
        rootMargin: '300px',
        threshold: 0,
      }

      let clb = (entries) => {

      entries.forEach(entry => {

        if(!entry.isIntersecting) {

          return
          
        } else {
          this.$emit('onObserve', true)
          observer.unobserve(entry.target)

        }
      });
    };

    let observer = new IntersectionObserver(clb, opts);
      observer.observe(el)
    },

  }
}
</script>
