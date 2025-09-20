<script setup lang="ts">
import { inject, computed } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
  class?: string
  value: string
}

defineOptions({
  inheritAttrs: false,
})

const props = defineProps<Props>()

const tabs = inject('tabs') as any
const isActive = computed(() => tabs.activeTab.value === props.value)

const handleClick = () => {
  tabs.setActiveTab(props.value)
}
</script>

<template>
  <button
    v-bind="$attrs"
    :class="
      cn(
        'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
        isActive ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground',
        props.class,
      )
    "
    @click="handleClick"
  >
    <slot />
  </button>
</template>