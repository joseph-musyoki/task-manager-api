import { ref, computed } from 'vue'
import { api } from './useApi'
import { useToast } from './useToast'

const { toast } = useToast()

export function useTasks() {
  const tasks        = ref([])
  const loading      = ref(false)
  const activeFilter = ref('')
  const editingTask  = ref(null)   // task currently open in the edit modal

  // ── Derived counts ─────────────────────────────────────────────
  const counts = computed(() => ({
    total:       tasks.value.length,
    pending:     tasks.value.filter(t => t.status === 'pending').length,
    in_progress: tasks.value.filter(t => t.status === 'in_progress').length,
    done:        tasks.value.filter(t => t.status === 'done').length,
  }))

  // ── Status helpers ─────────────────────────────────────────────
  const STATUS_NEXT  = { pending: 'in_progress', in_progress: 'done' }
  const STATUS_LABEL = { pending: 'Start', in_progress: 'Complete' }

  function nextStatus(current) { return STATUS_NEXT[current] ?? null }
  function nextLabel(current)  { return STATUS_LABEL[current] ?? '' }

  // ── Fetch ──────────────────────────────────────────────────────
  async function fetchTasks(status = activeFilter.value) {
    loading.value = true
    try {
      const { data } = await api.getTasks(status)
      tasks.value = (data.data || []).map(t => ({ ...t, _busy: false }))
    } catch {
      toast('Failed to load tasks. Is Laravel running?', 'error')
    } finally {
      loading.value = false
    }
  }

  // ── Filter ─────────────────────────────────────────────────────
  async function setFilter(val) {
    activeFilter.value = val
    await fetchTasks(val)
  }

  // ── Edit modal ─────────────────────────────────────────────────
  function openEdit(task)  { editingTask.value = { ...task } }
  function closeEdit()     { editingTask.value = null }

  // ── Advance status ─────────────────────────────────────────────
  async function advanceStatus(task) {
    const next = nextStatus(task.status)
    if (!next) return
    task._busy = true
    try {
      await api.updateStatus(task.id, next)
      toast(`Task moved to "${next.replace('_', ' ')}"`)
      await fetchTasks()
    } catch (err) {
      const msg = err.response?.data?.message || 'Status update failed'
      toast(msg, 'error')
    } finally {
      task._busy = false
    }
  }

  // ── Delete ─────────────────────────────────────────────────────
  async function deleteTask(task) {
    task._busy = true
    try {
      await api.deleteTask(task.id)
      toast('Task deleted')
      await fetchTasks()
    } catch (err) {
      const msg = err.response?.data?.message || 'Delete failed'
      toast(msg, 'error')
    } finally {
      task._busy = false
    }
  }

  return {
    tasks, loading, activeFilter, counts, editingTask,
    fetchTasks, setFilter, advanceStatus, deleteTask,
    openEdit, closeEdit,
    nextStatus, nextLabel,
  }
}