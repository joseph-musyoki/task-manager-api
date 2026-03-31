<template>
  <div>
    <!-- Filter tabs -->
    <div class="filter-tabs">
      <button
        v-for="f in FILTERS"
        :key="f.value"
        class="tab"
        :class="{ active: activeFilter === f.value }"
        @click="$emit('filter', f.value)"
      >
        {{ f.label }}
        <span v-if="f.value === activeFilter" class="tab-count">{{ tasks.length }}</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <span class="big-spinner"></span>
    </div>

    <!-- Empty -->
    <div v-else-if="tasks.length === 0" class="empty-state fade-up">
      <div class="empty-icon">◻</div>
      <p class="empty-title">No tasks here</p>
      <p class="empty-sub">Create one using the form →</p>
    </div>

    <!-- List -->
    <div v-else class="task-list">
      <TaskCard
        v-for="(task, i) in tasks"
        :key="task.id"
        :task="task"
        :style="{ animationDelay: `${i * 40}ms` }"
        @advance="$emit('advance', task)"
        @delete="onDelete(task)"
      />
    </div>
  </div>
</template>

<script setup>
import TaskCard from './TaskCard.vue'

defineProps({
  tasks:        { type: Array,   required: true },
  loading:      { type: Boolean, default: false },
  activeFilter: { type: String,  default: '' },
})

const emit = defineEmits(['filter', 'advance', 'delete'])

const FILTERS = [
  { label: 'All',         value: '' },
  { label: 'Pending',     value: 'pending' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Done',        value: 'done' },
]

function onDelete(task) {
  if (confirm(`Delete "${task.title}"? This cannot be undone.`)) {
    emit('delete', task)
  }
}
</script>

<style scoped>
/* Filter tabs */
.filter-tabs {
  display: flex; gap: 6px; flex-wrap: wrap;
  margin-bottom: 20px;
}

.tab {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 16px;
  border-radius: 20px;
  font-family: var(--font-mono);
  font-size: 0.72rem;
  letter-spacing: 0.5px;
  border: 1px solid var(--border);
  background: transparent;
  color: var(--muted);
  cursor: pointer;
  transition: all 0.15s;
}
.tab:hover { border-color: var(--border2); color: var(--text); }
.tab.active {
  background: var(--accent);
  color: #080810;
  border-color: var(--accent);
  font-weight: 600;
}

.tab-count {
  background: rgba(8,8,16,0.25);
  border-radius: 10px;
  padding: 1px 7px;
  font-size: 0.65rem;
}

/* Loading */
.loading-state {
  display: flex; justify-content: center; padding: 60px;
}
.big-spinner {
  width: 28px; height: 28px;
  border: 2px solid var(--border);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  display: block;
}

/* Empty */
.empty-state {
  text-align: center;
  padding: 70px 20px;
  color: var(--muted);
}
.empty-icon {
  font-size: 2.5rem;
  margin-bottom: 16px;
  opacity: 0.25;
  font-family: var(--font-ui);
  font-weight: 800;
}
.empty-title {
  font-family: var(--font-ui);
  font-size: 1rem;
  font-weight: 700;
  color: var(--subtle);
  margin-bottom: 6px;
}
.empty-sub {
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

/* List */
.task-list {
  display: flex; flex-direction: column; gap: 8px;
}
</style>