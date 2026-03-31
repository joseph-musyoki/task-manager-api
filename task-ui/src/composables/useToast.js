import { ref } from 'vue'

const toasts = ref([])

export function useToast() {
  function toast(message, type = 'success', duration = 3500) {
    const id = Date.now() + Math.random()
    toasts.value.push({ id, message, type })
    setTimeout(() => {
      toasts.value = toasts.value.filter(t => t.id !== id)
    }, duration)
  }

  return { toasts, toast }
}