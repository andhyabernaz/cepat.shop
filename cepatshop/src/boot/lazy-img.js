/**
 * v-lazy-img directive - Lazy loading images using IntersectionObserver
 * Usage: <img v-lazy-img="imageUrl" /> or <img v-lazy-img />
 *
 * Features:
 * - Uses native loading="lazy" as baseline
 * - IntersectionObserver for precise viewport detection
 * - Fade-in animation on load
 * - Placeholder background while loading
 */
import { boot } from 'quasar/wrappers'

const PLACEHOLDER = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3Crect fill="%23f1f5f9" width="1" height="1"/%3E%3C/svg%3E'

function createObserver () {
  if (typeof IntersectionObserver === 'undefined') return null

  return new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target
        const src = img.dataset.lazySrc

        if (src) {
          img.src = src
          img.removeAttribute('data-lazy-src')
          img.addEventListener('load', () => {
            img.style.opacity = '1'
          }, { once: true })
        }

        observer.unobserve(img)
      }
    })
  }, {
    rootMargin: '200px 0px', // Start loading 200px before viewport
    threshold: 0.01
  })
}

let observer = null

export default boot(async ({ app }) => {
  observer = createObserver()

  app.directive('lazy-img', {
    mounted (el, binding) {
      const src = binding.value || el.getAttribute('src')

      if (!src) return

      // Set native lazy loading
      el.setAttribute('loading', 'lazy')
      el.setAttribute('decoding', 'async')

      if (observer) {
        // Use IntersectionObserver
        el.dataset.lazySrc = src
        el.src = PLACEHOLDER
        el.style.opacity = '0'
        el.style.transition = 'opacity 0.3s ease'
        observer.observe(el)
      } else {
        // Fallback: just set src directly
        el.src = src
      }
    },

    updated (el, binding) {
      if (binding.value !== binding.oldValue && binding.value) {
        el.src = binding.value
      }
    },

    unmounted (el) {
      if (observer) {
        observer.unobserve(el)
      }
    }
  })
})
