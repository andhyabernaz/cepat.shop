<template>
   <article class="admin-feature-card" :style="{ animationDelay: `${index * 80}ms` }">
      <div class="admin-feature-card__icon">
         <q-icon :name="feature.icon" size="24px" />
      </div>
      <div class="admin-feature-card__title">{{ feature.title }}</div>
      <p class="admin-feature-card__description">{{ feature.description }}</p>
      <div class="admin-feature-card__tags">
         <span v-for="item in feature.items" :key="item" class="admin-feature-card__tag">{{ item }}</span>
      </div>
   </article>
</template>

<script>
export default {
   name: 'AdminLandingFeatureCard',
   props: {
      feature: {
         type: Object,
         required: true,
      },
      index: {
         type: Number,
         default: 0,
      },
   },
}
</script>

<style scoped>
.admin-feature-card {
   height: 100%;
   padding: 22px;
   border-radius: 24px;
   border: 1px solid var(--cs-border-light);
   background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
   box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
   transition: transform var(--cs-transition-normal), box-shadow var(--cs-transition-normal), border-color var(--cs-transition-fast);
   animation: adminFeatureFade 0.45s ease-out both;
}

.admin-feature-card:hover {
   transform: translateY(-4px);
   border-color: rgba(13, 158, 79, 0.28);
   box-shadow: 0 28px 50px rgba(15, 23, 42, 0.12);
}

.admin-feature-card__icon {
   width: 52px;
   height: 52px;
   display: inline-flex;
   align-items: center;
   justify-content: center;
   border-radius: 16px;
   background: rgba(13, 158, 79, 0.1);
   color: var(--cs-primary);
}

.admin-feature-card__title {
   margin-top: 18px;
   font-size: 1.1rem;
   font-weight: 700;
}

.admin-feature-card__description {
   margin-top: 10px;
   margin-bottom: 16px;
   color: var(--cs-text-secondary);
   line-height: 1.75;
}

.admin-feature-card__tags {
   display: flex;
   flex-wrap: wrap;
   gap: 8px;
}

.admin-feature-card__tag {
   display: inline-flex;
   padding: 6px 10px;
   border-radius: 999px;
   background: var(--cs-bg);
   color: var(--cs-text-secondary);
   font-size: 0.8rem;
   font-weight: 600;
}

@keyframes adminFeatureFade {
   from {
      opacity: 0;
      transform: translateY(14px);
   }

   to {
      opacity: 1;
      transform: translateY(0);
   }
}
</style>
