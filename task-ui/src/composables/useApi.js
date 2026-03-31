import axios from 'axios'

// ─── Change this to your Railway URL when deployed ───────────────
const BASE_URL = '/api'   // uses Vite proxy in dev → http://127.0.0.1:8000/api
// const BASE_URL = 'https://your-app.railway.app/api'  // production
// ─────────────────────────────────────────────────────────────────

const http = axios.create({
  baseURL: BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

export const api = {
  // GET /tasks?status=
  getTasks(status = '') {
    return http.get('/tasks', { params: status ? { status } : {} })
  },

  // POST /tasks
  createTask(payload) {
    return http.post('/tasks', payload)
  },

  // PUT /tasks/{id}  — update title, due_date, priority
  updateTask(id, payload) {
    return http.put(`/tasks/${id}`, payload)
  },

  // PATCH /tasks/{id}/status
  updateStatus(id, status) {
    return http.patch(`/tasks/${id}/status`, { status })
  },

  // DELETE /tasks/{id}
  deleteTask(id) {
    return http.delete(`/tasks/${id}`)
  },

  // GET /tasks/report?date=
  getReport(date) {
    return http.get('/tasks/report', { params: { date } })
  },
}