globalThis.IntersectionObserver =
  globalThis.IntersectionObserver ||
  class IntersectionObserver {
    constructor() {}
    observe() {}
    unobserve() {}
    disconnect() {}
    takeRecords() {
      return []
    }
  }

