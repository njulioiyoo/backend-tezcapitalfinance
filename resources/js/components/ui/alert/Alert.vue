<script setup lang="ts">
import { cn } from '@/lib/utils'

interface AlertProps {
  class?: string
  variant?: 'default' | 'destructive'
}

defineOptions({
  inheritAttrs: false,
})

const props = withDefaults(defineProps<AlertProps>(), {
  variant: 'default'
})
</script>

<template>
  <div
    v-bind="$attrs"
    :class="cn(
      'relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground',
      props.variant === 'destructive' 
        ? 'border-destructive/50 text-destructive bg-destructive/10 [&>svg]:text-destructive' 
        : 'bg-background text-foreground border-border',
      props.class
    )"
    role="alert"
  >
    <slot />
  </div>
</template>