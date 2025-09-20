<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const message = ref('Loading...');

onMounted(() => {
    message.value = 'Minimal Audit Log component loaded successfully!';
    // AuditLogMinimal component mounted
});
</script>

<template>
    <Head title="Audit Log Test" />
    
    <div class="p-8 bg-white dark:bg-gray-900 min-h-screen">
        <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">
            Audit Log Test
        </h1>
        
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <p class="font-bold">Success!</p>
            <p>{{ message }}</p>
            <p class="text-sm mt-2">
                This minimal component shows that Vue, Inertia, and the basic setup are working.
            </p>
        </div>
        
        <div class="mt-4 p-4 bg-blue-50 rounded">
            <h2 class="font-semibold mb-2">Debug Info:</h2>
            <ul class="text-sm space-y-1">
                <li>✅ Vue 3 reactive system working</li>
                <li>✅ Inertia Head component working</li>
                <li>✅ onMounted lifecycle working</li>
                <li>✅ Tailwind CSS working</li>
            </ul>
        </div>
    </div>
</template>