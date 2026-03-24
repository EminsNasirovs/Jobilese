<template>
  <Teleport to="body">
    <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-start gap-4 min-w-[300px] max-w-[420px] px-5 py-4 shadow-[0_8px_30px_rgba(0,0,0,0.12)] border"
          :class="{
            'bg-white border-black/[0.06]': toast.type === 'success',
            'bg-white border-red-100': toast.type === 'error',
            'bg-white border-blue-100': toast.type === 'info',
          }"
        >
          <!-- Icon -->
          <span class="mt-0.5 shrink-0">
            <svg v-if="toast.type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-emerald-500" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <svg v-else-if="toast.type === 'error'" width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-red-500" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6" stroke-linecap="round"/>
            </svg>
            <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-blue-500" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01" stroke-linecap="round"/>
            </svg>
          </span>

          <!-- Message -->
          <p class="text-[12px] font-medium text-gray-800 leading-relaxed flex-1">{{ toast.message }}</p>

          <!-- Close -->
          <button
            @click="remove(toast.id)"
            class="shrink-0 text-gray-300 hover:text-gray-600 transition-colors text-lg leading-none mt-0.5"
          >
            &times;
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'
const { toasts, remove } = useToast()
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-leave-active {
  transition: all 0.2s ease-in;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
