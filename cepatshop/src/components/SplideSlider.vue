<template>
  <div class="slider-container">
    <section ref="slider" class="splide splide-slider">
      <div class="splide__track">
        <ul class="splide__list">
          <li class="splide__slide" v-for="(img, index) in sliders" :key="index" @click="showPost(img)">
            <div class="slider-image">
              <img :src="img.src" />
            </div>
          </li>
        </ul>
      </div>
    </section>
  </div>
</template>

<script>
import Splide from '@splidejs/splide'
import '@splidejs/splide/dist/css/splide.min.css';
import '@splidejs/splide/dist/css/themes/splide-default.min.css'

export default {
  props: {
    sliders: Array
  },
  data() {
    return {
      splideSlider: null,
      options: {
        type: 'loop',
        padding: 0,
        start: 1,
        autoplay: false,
        autoHeight: true,
        autoplay: true,
        interval: 4000
      },
      sliderTimeout: null
    }
  },
  computed: {
    window_width() {
      return this.$store.state.window_width
    },
    is_mode_desktop() {
      return this.$store.getters['isModeDesktop']
    },
    config() {
      return this.$store.state.config
    }
  },
  watch: {
    window_width(val) {
      clearTimeout(this.sliderTimeout)
      this.setOptionSlider(val)
      this.sliderTimeout = setTimeout(() => this.initialSlider(), 500)
    }
  },
  mounted() {
    this.setOptionSlider(this.window_width)

    setTimeout(() => this.initialSlider(), 200)

  },
  methods: {
    showPost(item) {
      if (item.post) {
        this.$router.push({ name: 'FrontPostShow', params: { slug: item.post.slug } })
      }
    },
    mountSlider() {
      this.splideSlider.mount()
    },
    initialSlider() {
      this.splideSlider = new Splide('.splide', this.options);
      this.mountSlider()
    },
    setOptionSlider(width) {
      if(this.config) {
        this.options.interval = this.config.slider_interval ? parseInt(this.config.slider_interval) * 1000 : 4000
        this.options.autoplay = this.config.slider_autoplay 
      }

      if (this.is_mode_desktop) {
        if (this.config.is_featured_slider) {
          this.$refs.slider.classList.add('is-featured')
          if (width < 575) {
            this.options.padding = 0
          }
          if (width >= 575 && width < 800) {
            this.options.padding = '10%'
          }
          if (width >= 800 && width < 1024) {
            this.options.padding = '15%'
          }
          if (width >= 1024) {
            this.options.padding = '18%'
          }
          if (width >= 1280) {
            this.options.padding = '22%'
          } else {
            this.$refs.slider.classList.remove('is-featured')
          }
        }
        this.options.autoHeight = false
      } else {
        this.options.padding = 0
        this.options.autoHeight = true
      }
    },
  }
};
</script>
<style lang="scss">
.slider-container {
  padding-top: 0px;
  padding-bottom: 0px;
}

.splide-slider {
  .splide__slide {
    padding: 0;

    .slider-image img {
      width: 100%;
      height: auto;
    }
  }
}

@media screen and (min-width: 768px) {
  .desktop-view {

    .slider-container {
      padding-top: 2rem;
      padding-bottom: 1rem;

      .splide__list {
        align-items: center;
      }

      .splide-slider {
        position: relative;

        .splide__pagination {
          position: relative;
          bottom: 40px;
        }

        .splide__slide {
          padding-top: 20px;
          padding-bottom: 20px;

          .slider-image {
            z-index: 5;
            position: relative;

            img {
              width: 100%;
              height: auto;
            }

          }

        }

        &.is-featured {
          .slider-image {
            background-color: rgb(173, 173, 173);
            transition: transform 300ms ease-in-out;
            transform: scale(.90);
            position: relative;
            border-radius: 10px;

            img {
              transition: opacity 200ms;
              opacity: .8;
            }

          }

          .is-active.is-visible .slider-image {
            transform: scale(1.05);
            z-index: 10;
            border: 5px solid #fff;

            img {
              transition: all 300ms ease-in-out;
              opacity: 1;
            }
          }
        }
      }
    }

  }
}
</style>