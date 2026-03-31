<template>
  <div class="task-card" :class="`priority-${task.priority}`">
    <!-- Priority stripe -->
    <div class="priority-stripe"></div>

    <div class="card-body">
      <div class="card-top">
        <div class="task-title">{{ task.title }}</div>
        <div class="task-actions">
          <!-- Edit -->
          <button
            class="action-btn edit"
            :disabled="task._busy"
            @click="$emit('edit', task)"
            title="Edit task"
          >✎</button>

          <!-- Advance status -->
          <button
            v-if="nextStatus"
            class="action-btn advance"
            :disabled="task._busy"
            @click="$emit('advance', task)"
            :title="`Move to ${nextStatus}`"
          >
            <span v-if="task._busy" class="spinner-sm"></span>
            <span v-else>{{ nextLabel }} →</span>
          </button>

          <!-- Delete (only for done tasks) -->
          <button
            v-if="task.status === 'done'"
            class="action-btn delete"
            :disabled="task._busy"
            @click="$emit('delete', task)"
            title="Delete task"
          >
            <span v-if="task._busy" class="spinner-sm"></span>
            <span v-else>✕</span>
          </button>
        </div>
      </div>

      <div class="card-meta">
        <span class="badge" :class="`badge-priority-${task.priority}`">{{ task.priority }}</span>
        <span class="badge" :class="`badge-status-${task.status}`">{{ task.status.replace('_', ' ') }}</span>
        <span class="due-date">📅 {{ task.due_date }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  task: { type: Object, required: true },
})

defineEmits(['advance', 'delete', 'edit'])

const NEXT_STATUS = { pending: 'in_progress', in_progress: 'done' }
const NEXT_LABEL  = { pending: 'Start', in_progress: 'Complete' }

const nextStatus = computed(() => NEXT_STATUS[props.task.status] ?? null)
const nextLabel  = computed(() => NEXT_LABEL[props.task.status] ?? '')
</script>

<style scoped>
.task-card {
  display: flex;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  overflow: hidden;
  transition: border-color 0.2s, transform 0.15s;
  animation: fadeUp 0.3s ease both;
}
.task-card:hover {
  border-color: var(--border2);
  transform: translateX(3px);
}

/* Left colour stripe */
.priority-stripe {
  width: 3px;
  flex-shrink: 0;
}
.priority-high   .priority-stripe { background: var(--high); }
.priority-medium .priority-stripe { background: var(--medium); }
.priority-low    .priority-stripe { background: var(--low); }

.card-body {
  flex: 1;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.task-title {
  font-family: var(--font-ui);
  font-size: 0.92rem;
  font-weight: 600;
  line-height: 1.3;
  flex: 1;
}

.task-actions { display: flex; gap: 6px; flex-shrink: 0; }

.action-btn {
  padding: 5px 12px;
  border-radius: var(--r-sm);
  font-family: var(--font-mono);
  font-size: 0.72rem;
  font-weight: 500;
  border: 1px solid;
  cursor: pointer;
  display: flex; align-items: center; gap: 5px;
  transition: all 0.15s;
  white-space: nowrap;
  background: transparent;
}

.action-btn.edit {
  color: var(--accent);
  border-color: rgba(200,255,87,0.3);
}
.action-btn.edit:hover:not(:disabled) {
  background: rgba(200,255,87,0.08);
  border-color: var(--accent);
}

.action-btn.advance {
  color: var(--accent2);
  border-color: rgba(87,232,255,0.3);
}
.action-btn.advance:hover:not(:disabled) {
  background: rgba(87,232,255,0.08);
  border-color: var(--accent2);
}

.action-btn.delete {
  color: var(--danger);
  border-color: rgba(255,79,106,0.3);
}
.action-btn.delete:hover:not(:disabled) {
  background: rgba(255,79,106,0.08);
  border-color: var(--danger);
}

.action-btn:disabled { opacity: 0.3; cursor: not-allowed; }

/* Meta row */
.card-meta {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}

.badge {
  font-size: 0.62rem;
  padding: 3px 8px;
  border-radius: 4px;
  letter-spacing: 1px;
  text-transform: uppercase;
  font-weight: 500;
  border: 1px solid;
}

/* Priority badges */
.badge-priority-high   { color: var(--high);   border-color: rgba(255,107,129,0.4); background: rgba(255,107,129,0.08); }
.badge-priority-medium { color: var(--medium); border-color: rgba(255,179,71,0.4);  background: rgba(255,179,71,0.08); }
.badge-priority-low    { color: var(--low);    border-color: rgba(87,232,135,0.4);  background: rgba(87,232,135,0.08); }

/* Status badges */
.badge-status-pending     { color: var(--muted); border-color: var(--border2); background: rgba(100,100,160,0.1); }
.badge-status-in_progress { color: var(--accent2); border-color: rgba(87,232,255,0.3); background: rgba(87,232,255,0.06); }
.badge-status-done        { color: var(--low); border-color: rgba(87,232,135,0.4); background: rgba(87,232,135,0.08); }

.due-date {
  font-size: 0.68rem;
  color: var(--muted);
  margin-left: auto;
}

.spinner-sm {
  width: 12px; height: 12px;
  border: 1.5px solid currentColor;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}
</style>