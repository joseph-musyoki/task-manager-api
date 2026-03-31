<template>
  <div class="panel">
    <div class="panel-header">
      <span class="panel-title">Daily Report</span>
    </div>

    <div class="panel-body">
      <div class="report-controls">
        <input v-model="date" type="date" class="date-input" />
        <button class="run-btn" @click="fetch" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          <span v-else>Run</span>
        </button>
      </div>

      <div v-if="error" class="alert-error slide-down">{{ error }}</div>

      <div v-if="report" class="report-body fade-up">
        <div class="report-date">{{ report.date }}</div>
        <div class="report-grid">
          <div v-for="p in PRIORITIES" :key="p" class="report-col">
            <div class="col-header" :style="{ color: PRIORITY_COLORS[p] }">
              {{ p }}
            </div>
            <div v-for="s in STATUSES" :key="s" class="col-row">
              <span class="row-label">{{ s.replace('_', ' ') }}</span>
              <span class="row-val" :class="{ nonzero: report.summary[p][s] > 0 }">
                {{ report.summary[p][s] }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="!loading" class="empty-hint">
        Pick a date and hit Run
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { api } from '../composables/useApi'

const date    = ref(new Date().toISOString().split('T')[0])
const loading = ref(false)
const report  = ref(null)
const error   = ref('')

const PRIORITIES = ['high', 'medium', 'low']
const STATUSES   = ['pending', 'in_progress', 'done']
const PRIORITY_COLORS = {
  high: 'var(--high)', medium: 'var(--medium)', low: 'var(--low)',
}

async function fetch() {
  if (!date.value) { error.value = 'Select a date.'; return }
  error.value  = ''
  report.value = null
  loading.value = true
  try {
    const { data } = await api.getReport(date.value)
    report.value = data
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to fetch report.'
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
  margin-top: 16px;
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

.panel-body { padding: 18px 22px; }

.report-controls {
  display: flex; gap: 8px; margin-bottom: 16px;
}

.date-input {
  flex: 1;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--r-sm);
  color: var(--text);
  font-family: var(--font-mono);
  font-size: 0.82rem;
  padding: 9px 12px;
  outline: none;
  transition: border-color 0.2s;
}
.date-input:focus { border-color: var(--accent); }

.run-btn {
  padding: 9px 18px;
  background: var(--accent);
  color: #080810;
  border: none;
  border-radius: var(--r-sm);
  font-family: var(--font-ui);
  font-size: 0.78rem;
  font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; gap: 6px;
  transition: filter 0.15s;
  flex-shrink: 0;
}
.run-btn:hover:not(:disabled) { filter: brightness(1.1); }
.run-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.alert-error {
  font-size: 0.75rem;
  color: #ff8a9a;
  background: rgba(255,79,106,0.08);
  border: 1px solid var(--danger);
  border-radius: var(--r-sm);
  padding: 8px 12px;
  margin-bottom: 14px;
}

/* Report grid */
.report-date {
  font-family: var(--font-ui);
  font-size: 0.7rem;
  letter-spacing: 2px;
  color: var(--muted);
  text-transform: uppercase;
  margin-bottom: 14px;
}

.report-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.report-col {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--r-sm);
  padding: 12px;
}

.col-header {
  font-family: var(--font-ui);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-bottom: 10px;
}

.col-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
  font-size: 0.7rem;
}

.row-label { color: var(--muted); }
.row-val   { color: var(--subtle); font-weight: 500; }
.row-val.nonzero { color: var(--accent); }

.empty-hint {
  text-align: center;
  font-size: 0.75rem;
  color: var(--muted);
  padding: 24px 0 8px;
}

.spinner {
  width: 14px; height: 14px;
  border: 2px solid rgba(8,8,16,0.3);
  border-top-color: #080810;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
</style>