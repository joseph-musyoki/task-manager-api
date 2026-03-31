<template>
  <div class="stats-bar">
    <div class="stat" v-for="s in stats" :key="s.label">
      <span class="stat-num" :style="{ color: s.color }">{{ s.value }}</span>
      <span class="stat-label">{{ s.label }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  counts: { type: Object, required: true },
})

const stats = computed(() => [
  { label: 'Total',       value: props.counts.total,       color: 'var(--text)'    },
  { label: 'Pending',     value: props.counts.pending,     color: 'var(--muted)'   },
  { label: 'In Progress', value: props.counts.in_progress, color: 'var(--accent2)' },
  { label: 'Done',        value: props.counts.done,        color: 'var(--low)'     },
])
</script>

<style scoped>
.stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  margin-bottom: 24px;
}

.stat {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-num {
  font-family: var(--font-ui);
  font-size: 1.9rem;
  font-weight: 800;
  line-height: 1;
}

.stat-label {
  font-size: 0.65rem;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
}

@media (max-width: 600px) {
  .stats-bar { grid-template-columns: repeat(2, 1fr); }
}
</style>