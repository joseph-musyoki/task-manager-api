<template>
  <Teleport to="body">
    <div class="toast-wrap">
      <TransitionGroup name="toast">
        <div
          v-for="t in toasts"
          :key="t.id"
          class="toast"
          :class="t.type"
        >
          <span class="toast-icon">{{ t.type === 'success' ? '✓' : '✕' }}</span>
          <span class="toast-msg">{{ t.message }}</span>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast'
const { toasts } = useToast()
</script>

<style scoped>
.toast-wrap {
  position: fixed;
  bottom: 28px; right: 28px;
  display: flex; flex-direction: column; gap: 8px;
  z-index: 9999;
  pointer-events: none;
}

.toast {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 18px;
  border-radius: var(--r-sm);
  border: 1px solid;
  background: var(--card2);
  font-size: 0.78rem;
  max-width: 320px;
  box-shadow: var(--shadow);
}

.toast.success { border-color: var(--accent);  color: var(--accent); }
.toast.error   { border-color: var(--danger); color: #ff8a9a; }

.toast-icon { font-weight: 700; flex-shrink: 0; }
.toast-msg  { color: var(--text); }

/* TransitionGroup */
.toast-enter-active { animation: toastIn 0.25s ease; }
.toast-leave-active { animation: toastIn 0.2s ease reverse; }
</style>