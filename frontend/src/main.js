import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './styles/global.css'

try {
  const { default: Echo } = await import('laravel-echo')
  const { default: Pusher } = await import('pusher-js')
  window.Pusher = Pusher
  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: `${(import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/$/, '')}/broadcasting/auth`,
    auth: {
      headers: {
        get Authorization() {
          return `Bearer ${localStorage.getItem('token')}`
        },
      },
    },
  })
} catch (e) {
  console.warn('Reverb/Echo not available, real-time chat disabled:', e.message)
}

const app = createApp(App)
app.use(router)
app.mount('#app')
