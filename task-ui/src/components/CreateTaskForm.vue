<template>
  <div class="panel">
    <div class="panel-header">
      <span class="panel-title">New Task</span>
    </div>

    <form class="panel-body" @submit.prevent="submit">
      <!-- Error -->
      <div v-if="error" class="alert alert-error slide-down">
        <span class="alert-icon">!</span>
        <span>{{ error }}</span>
      </div>

      <!-- Success -->
      <div v-if="created" class="alert alert-success slide-down">
        <span class="alert-icon">✓</span>
        <span>Task created successfully</span>
      </div>

      <div class="field">
        <label for="title">Title</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          placeholder="e.g. Review pull request"
          autocomplete="off"
        />
      </div>

      <div class="field">
        <label for="due_date">Due Date</label>
        <input
          id="due_date"
          v-model="form.due_date"
          type="date"
          :min="today"
        />
      </div>

      <div class="field">
        <label for="priority">Priority</label>
        <select id="priority" v-model="form.priority">
          <option value="">Select priority…</option>
          <option value="high">High</option>
          <option value="medium">Medium</option>
          <option value="low">Low</option>
        </select>
      </div>

      <button type="submit" class="btn-submit" :disabled="loading">
        <span v-if="loading" class="spinner"></span>
        <span v-else>+ Create Task</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { api } from '../composables/useApi'
import { useToast } from '../composables/useToast'

const emit = defineEmits(['created'])
const { toast } = useToast()

const today = new Date().toISOString().split('T')[0]

const form = ref({ title: '', due_date: today, priority: '' })
const loading = ref(false)
const error   = ref('')
const created = ref(false)

async function submit() {
  error.value   = ''
  created.value = false

  if (!form.value.title.trim())  { error.value = 'Title is required.'; return }
  if (!form.value.due_date)      { error.value = 'Due date is required.'; return }
  if (!form.value.priority)      { error.value = 'Priority is required.'; return }

  loading.value = true
  try {
    await api.createTask({
      title:    form.value.title.trim(),
      due_date: form.value.due_date,
      priority: form.value.priority,
    })

    created.value    = true
    form.value.title    = ''
    form.value.priority = ''
    toast('Task created!')
    emit('created')
    setTimeout(() => { created.value = false }, 3000)
  } catch (err) {
    const errs = err.response?.data?.errors
    if (errs) {
      error.value = Object.values(errs).flat().join(' ')
    } else {
      error.value = err.response?.data?.message || 'Something went wrong.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.panel {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--r-lg);
  overflow: hidden;
}

.panel-header {
  padding: 16px 22px 14px;
  border-bottom: 1px solid var(--border);
}

.panel-title {
  font-family: var(--font-ui);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--accent);
}

.panel-body { padding: 22px; display: flex; flex-direction: column; gap: 0; }

/* Alerts */
.alert {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 0.78rem;
  padding: 10px 14px;
  border-radius: var(--r-sm);
  margin-bottom: 16px;
  border: 1px solid;
  line-height: 1.4;
}
.alert-error   { background: rgba(255,79,106,0.08); border-color: var(--danger); color: #ff8a9a; }
.alert-success { background: rgba(200,255,87,0.06); border-color: var(--accent); color: var(--accent); }
.alert-icon { font-weight: 700; flex-shrink: 0; }

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
select option { background: var(--surface); }
input::placeholder { color: var(--subtle); }

/* Submit */
.btn-submit {
  width: 100%;
  margin-top: 4px;
  padding: 12px;
  background: var(--accent);
  color: #080810;
  border: none;
  border-radius: var(--r-sm);
  font-family: var(--font-ui);
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 1px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: filter 0.15s, transform 0.15s;
}
.btn-submit:hover:not(:disabled) { filter: brightness(1.1); transform: translateY(-1px); }
.btn-submit:disabled { opacity: 0.4; cursor: not-allowed; }

.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(8,8,16,0.3);
  border-top-color: #080810;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
</style>