<template>
   <section
      :id="sectionId"
      class="admin-landing-section"
      :class="[
         `admin-landing-section--${align}`,
         { 'admin-landing-section--soft': soft },
      ]"
   >
      <div v-if="eyebrow || title || description" class="admin-landing-section__intro">
         <div v-if="eyebrow" class="admin-landing-section__eyebrow">{{ eyebrow }}</div>
         <h2 v-if="title" class="admin-landing-section__title">{{ title }}</h2>
         <p v-if="description" class="admin-landing-section__description">{{ description }}</p>
      </div>
      <div class="admin-landing-section__body">
         <slot />
      </div>
   </section>
</template>

<script>
export default {
   name: 'AdminLandingSection',
   props: {
      sectionId: {
         type: String,
         default: '',
      },
      eyebrow: {
         type: String,
         default: '',
      },
      title: {
         type: String,
         default: '',
      },
      description: {
         type: String,
         default: '',
      },
      align: {
         type: String,
         default: 'left',
      },
      soft: {
         type: Boolean,
         default: false,
      },
   },
}
</script>

<style scoped>
.admin-landing-section {
   position: relative;
   padding: 28px;
   border-radius: 32px;
   background: var(--cs-surface);
   border: 1px solid var(--cs-border-light);
   box-shadow: var(--cs-shadow-card);
}

.admin-landing-section--soft {
   background:
      radial-gradient(circle at top left, rgba(13, 158, 79, 0.08), transparent 36%),
      var(--cs-surface);
}

.admin-landing-section__intro {
   margin-bottom: 20px;
   max-width: 720px;
}

.admin-landing-section--center .admin-landing-section__intro {
   margin-left: auto;
   margin-right: auto;
   text-align: center;
}

.admin-landing-section__eyebrow {
   display: inline-flex;
   padding: 6px 12px;
   border-radius: 999px;
   background: rgba(13, 158, 79, 0.1);
   color: var(--cs-primary);
   font-size: 0.78rem;
   font-weight: 700;
   letter-spacing: 0.08em;
   text-transform: uppercase;
}

.admin-landing-section__title {
   margin-top: 14px;
   margin-bottom: 10px;
   font-size: clamp(1.55rem, 2.4vw, 2.25rem);
   line-height: 1.05;
}

.admin-landing-section__description {
   margin: 0;
   color: var(--cs-text-secondary);
   font-size: 1rem;
   line-height: 1.75;
}

@media (max-width: 767px) {
   .admin-landing-section {
      padding: 20px;
      border-radius: 24px;
   }

   .admin-landing-section__description {
      font-size: 0.94rem;
      line-height: 1.7;
   }
}
</style>
