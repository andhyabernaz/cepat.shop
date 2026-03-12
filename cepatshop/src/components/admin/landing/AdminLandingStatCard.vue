<template>
   <article class="admin-stat-card">
      <div class="admin-stat-card__head">
         <div class="admin-stat-card__icon" :class="`admin-stat-card__icon--${accent}`">
            <q-icon :name="item.icon || 'insights'" size="22px" />
         </div>
         <div class="admin-stat-card__label">{{ item.label }}</div>
      </div>
      <div class="admin-stat-card__value">{{ item.value }}</div>
      <p class="admin-stat-card__description">{{ item.description }}</p>
   </article>
</template>

<script>
export default {
   name: 'AdminLandingStatCard',
   props: {
      item: {
         type: Object,
         required: true,
      },
   },
   computed: {
      accent() {
         return (this.item.accent || this.item.color || 'primary').replace(/[^a-z0-9-]/gi, '-')
      },
   },
}
</script>

<style scoped>
.admin-stat-card {
   height: 100%;
   padding: 22px;
   border-radius: 24px;
   border: 1px solid var(--cs-border-light);
   background: var(--cs-surface);
   box-shadow: var(--cs-shadow-card);
}

.admin-stat-card__head {
   display: flex;
   align-items: center;
   gap: 12px;
}

.admin-stat-card__icon {
   width: 48px;
   height: 48px;
   display: inline-flex;
   align-items: center;
   justify-content: center;
   border-radius: 16px;
   background: rgba(13, 158, 79, 0.1);
   color: var(--cs-primary);
}

.admin-stat-card__icon--positive,
.admin-stat-card__icon--green {
   background: rgba(34, 197, 94, 0.12);
   color: #15803d;
}

.admin-stat-card__icon--amber-8,
.admin-stat-card__icon--orange {
   background: rgba(245, 158, 11, 0.14);
   color: #b45309;
}

.admin-stat-card__icon--red {
   background: rgba(239, 68, 68, 0.12);
   color: #b91c1c;
}

.admin-stat-card__icon--purple {
   background: rgba(139, 92, 246, 0.12);
   color: #6d28d9;
}

.admin-stat-card__icon--teal {
   background: rgba(20, 184, 166, 0.12);
   color: #0f766e;
}

.admin-stat-card__label {
   font-size: 0.85rem;
   font-weight: 700;
   letter-spacing: 0.05em;
   text-transform: uppercase;
   color: var(--cs-text-secondary);
}

.admin-stat-card__value {
   margin-top: 18px;
   font-size: clamp(1.45rem, 2.3vw, 2.35rem);
   font-weight: 800;
   line-height: 1;
}

.admin-stat-card__description {
   margin-top: 10px;
   margin-bottom: 0;
   color: var(--cs-text-secondary);
   line-height: 1.7;
}
</style>
