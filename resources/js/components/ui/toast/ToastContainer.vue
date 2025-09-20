<template>
  <Teleport to="body">
    <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 flex flex-col gap-2">
      <TransitionGroup
        name="toast"
        tag="div"
        class="flex flex-col gap-2"
      >
        <div
          v-for="toast in toastState.toasts"
          :key="toast.id"
          v-show="toast.visible"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg border max-w-md min-w-80',
            'backdrop-blur-sm',
            toast.variant === 'destructive' 
              ? 'bg-destructive/10 border-destructive/20 text-destructive dark:bg-destructive/5 dark:border-destructive/30 dark:text-destructive'
              : 'bg-background/90 border-border text-foreground dark:bg-background/90 dark:border-border dark:text-foreground'
          ]"
        >
          <!-- Icon -->
          <div class="flex-shrink-0">
            <CheckCircle 
              v-if="toast.variant !== 'destructive'" 
              class="w-5 h-5 text-emerald-600 dark:text-emerald-400" 
            />
            <XCircle 
              v-else 
              class="w-5 h-5 text-destructive" 
            />
          </div>
          
          <!-- Content -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium">{{ toast.title }}</p>
            <p v-if="toast.description" class="text-sm opacity-80 mt-1">{{ toast.description }}</p>
          </div>
          
          <!-- Close button -->
          <button
            @click="removeToast(toast.id)"
            class="flex-shrink-0 p-1 rounded-md hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { CheckCircle, XCircle, X } from 'lucide-vue-next'
import { useToastState, removeToast } from '@/composables/useToast'

const toastState = useToastState()
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease-out;
}

.toast-enter-from {
  opacity: 0;
  transform: translateY(8px) scale(0.95);
}

.toast-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>