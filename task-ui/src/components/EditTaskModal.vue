<template>
  <Teleport to="body">
    <div class="overlay" @click.self="$emit('close')">
      <div class="modal slide-down">
        <!-- Header -->
        <div class="modal-header">
          <span class="modal-title">Edit Task</span>
          <button class="close-btn" @click="$emit('close')">✕</button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div v-if="error" class="alert-error slide-down">{{ error }}</div>

          <div class="field">
            <label>Title</label>
            <input v-model="form.title" type="text" placeholder="Task title" />
          </div>

          <div class="field">
            <label>Due Date</label>
            <input v-model="form.due_date" type="date" :min="today" />
          </div>

          <div class="field">
            <label>Priority</label>
            <select v-model="form.priority">
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>

          <div class="field">
            <label>Status</label>
            <select v-model="form.status" disabled title="Use the task card buttons to change status">
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="done">Done</option>
            </select>
            <span class="field-hint">Status changes via task card buttons only</span>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button class="btn-cancel" @click="$emit('close')">Cancel</button>
          <button class="btn-save" @click="submit" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span v-else>Save Changes</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { api } from '../composables/useApi'
import { useToast } from '../composables/useToast'

const props = defineProps({
  task: { type: Object, required: true },
})

const emit = defineEmits(['close', 'updated'])
const { toast } = useToast()

const today   = new Date().toISOString().split('T')[0]
const loading = ref(false)
const error   = ref('')

const form = ref({ ...props.task })

// Sync if parent passes a different task
watch(() => props.task, (t) => { form.value = { ...t } })

async function submit() {
  error.value = ''

  if (!form.value.title.trim())  { error.value = 'Title is required.'; return }
  if (!form.value.due_date)      { error.value = 'Due date is required.'; return }
  if (!form.value.priority)      { error.value = 'Priority is required.'; return }

  loading.value = true
  try {
    await api.updateTask(props.task.id, {
      title:    form.value.title.trim(),
      due_date: form.value.due_date,
      priority: form.value.priority,
    })
    toast('Task updated!')
    emit('updated')
    emit('close')
  } catch (err) {
    const errs = err.response?.data?.errors
    if (errs) {
      error.value = Object.values(errs).flat().join(' ')
    } else {
      error.value = err.response?.data?.message || 'Update failed.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Overlay */
.overlay {
  position: fixed; inset: 0;
  background: rgba(8, 8, 16, 0.75);
  backdrop-filter: blur(6px);
  z-index: 500;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
}

/* Modal */
.modal {
  width: 100%;
  max-width: 440px;
  background: var(--card);
  border: 1px solid var(--border2);
  border-radius: var(--r-lg);
  overflow: hidden;
  box-shadow: 0 24px 60px rgba(0,0,0,0.6);
}

.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 22px 16px;
  border-bottom: 1px solid var(--border);
}

.modal-title {
  font-family: var(--font-ui);
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--accent);
}

.close-btn {
  background: none; border: none;
  color: var(--muted); cursor: pointer;
  font-size: 0.85rem; padding: 4px 8px;
  border-radius: 4px;
  transition: color 0.15s, background 0.15s;
}
.close-btn:hover { color: var(--text); background: var(--border); }

.modal-body { padding: 22px; display: flex; flex-direction: column; gap: 0; }

/* Alert */
.alert-error {
  font-size: 0.78rem;
  padding: 10px 14px;
  border-radius: var(--r-sm);
  margin-bottom: 16px;
  border: 1px solid var(--danger);
  background: rgba(255,79,106,0.08);
  color: #ff8a9a;
}

/* Fields */
.field { margin-bottom: 16px; }

label {
  display: block;
  font-size: 0.65rem;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 7px;
}

input, select {
  width: 100%;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--r-sm);
  color: var(--text);
  font-family: var(--font-mono);
  font-size: 0.85rem;
  padding: 10px 12px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
input:focus, select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(200,255,87,0.08);
}
select:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}
select option { background: var(--surface); }

.field-hint {
  display: block;
  font-size: 0.63rem;
  color: var(--muted);
  margin-top: 5px;
  letter-spacing: 0.5px;
}

/* Footer */
.modal-footer {
  display: flex; gap: 8px; justify-content: flex-end;
  padding: 16px 22px 20px;
  border-top: 1px solid var(--border);
}

.btn-cancel {
  padding: 10px 18px;
  background: transparent;
  border: 1px solid var(--border2);
  border-radius: var(--r-sm);
  color: var(--muted);
  font-family: var(--font-mono);
  font-size: 0.78rem;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-cancel:hover { border-color: var(--text); color: var(--text); }

.btn-save {
  padding: 10px 22px;
  background: var(--accent);
  color: #080810;
  border: none;
  border-radius: var(--r-sm);
  font-family: var(--font-ui);
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; gap: 8px;
  transition: filter 0.15s;
}
.btn-save:hover:not(:disabled) { filter: brightness(1.1); }
.btn-save:disabled { opacity: 0.4; cursor: not-allowed; }

.spinner {
  width: 14px; height: 14px;
  border: 2px solid rgba(8,8,16,0.3);
  border-top-color: #080810;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
</style>