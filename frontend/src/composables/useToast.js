import { ref } from 'vue'

const toasts = ref([])
let nextId = 0

export function useToast() {
  const add = (message, type = 'success', duration = 3500) => {
    const id = nextId++
    toasts.value.push({ id, message, type })
    setTimeout(() => remove(id), duration)
  }

  const remove = (id) => {
    const idx = toasts.value.findIndex(t => t.id === id)
    if (idx !== -1) toasts.value.splice(idx, 1)
  }

  const success = (msg) => add(msg, 'success')
  const error = (msg) => add(msg, 'error')
  const info = (msg) => add(msg, 'info')

  return { toasts, add, remove, success, error, info }
}
