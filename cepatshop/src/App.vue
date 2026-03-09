<template>
   <div class="main-page">
        <q-resize-observer @resize="onResize" />
      <router-view />
   </div>
</template>

<script setup>
defineOptions({
   name: 'App',
   computed: {
      session_id() {
         return this.$store.state.session_id;
      },
      config() {
         return this.$store.state.config;
      },
   },
   methods: {
      onResize(ev) {
         this.$store.commit("SET_PAGE_WIDTH", ev.width);   
      },
      pageResize() {
         this.$store.commit("SET_WINDOW_WIDTH", window.innerWidth);
      },
   },
   mounted() {
      if (window.innerWidth < 1024) {
         this.$store.commit("SET_DRAWER", false);
      }
   },
   created() {
      
      window.addEventListener("resize", this.pageResize);

      if (this.config) {
         this.$store.commit("SET_CURRENT_THEME");
      }

      this.$store.commit("REMOVE_INSTALL_APP");

      window.addEventListener("beforeinstallprompt", (e) => {
         this.$store.commit("SET_INSTALL_APP", e);
      });

      window.addEventListener("appinstalled", () => {
         this.$store.commit("REMOVE_INSTALL_APP");
      });

   },
   beforeUnmount() {
      window.removeEventListener("resize", this.pageResize);
      window.removeEventListener("beforeinstallprompt");
   },
   meta: {
      meta: {
         equiv: {
            "http-equiv": "Content-Type",
            content: "text/html; charset=UTF-8",
         },
         ogUrl: { property: "og:url", content: location.href },
      },
      noscript: {
         default: "This is content for browsers with no JS (or disabled JS)",
      },
   },
});
</script>
