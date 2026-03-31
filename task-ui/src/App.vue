<template>
  <div class="app-shell">
    <AppHeader />

    <main class="main">
      <StatsBar :counts="counts" />

      <div class="layout">
        <!-- Left sidebar: form + report -->
        <aside class="sidebar">
          <CreateTaskForm @created="fetchTasks" />
          <DailyReport />
        </aside>

        <!-- Right: task list -->
        <section class="content">
          <TaskList
            :tasks="tasks"
            :loading="loading"
            :active-filter="activeFilter"
            @filter="setFilter"
            @advance="advanceStatus"
            @edit="openEdit"
            @delete="deleteTask"
          />
        </section>
      </div>
    </main>

    <!-- Edit modal -->
    <EditTaskModal
      v-if="editingTask"
      :task="editingTask"
      @close="closeEdit"
      @updated="fetchTasks"
    />

    <ToastContainer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import AppHeader      from './components/AppHeader.vue'
import StatsBar       from './components/StatsBar.vue'
import CreateTaskForm from './components/CreateTaskForm.vue'
import TaskList       from './components/TaskList.vue'
import DailyReport    from './components/DailyReport.vue'
import EditTaskModal  from './components/EditTaskModal.vue'
import ToastContainer from './components/ToastContainer.vue'
import { useTasks }   from './composables/useTasks'

const {
  tasks, loading, activeFilter, counts, editingTask,
  fetchTasks, setFilter, advanceStatus, deleteTask,
  openEdit, closeEdit,
} = useTasks()

onMounted(fetchTasks)
</script>

<style scoped>
.app-shell { min-height: 100vh; display: flex; flex-direction: column; }

.main {
  flex: 1;
  max-width: 1140px;
  margin: 0 auto;
  width: 100%;
  padding: 36px 24px 80px;
}

.layout {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 24px;
  align-items: start;
}

.sidebar  { display: flex; flex-direction: column; }
.content  { min-width: 0; }

@media (max-width: 860px) {
  .layout { grid-template-columns: 1fr; }
  .sidebar { order: 2; }
  .content { order: 1; }
}
</style>