<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
        <AppHeader />

        <!-- Flash messages -->
        <div v-if="flash.success || flash.error || flash.warning" class="px-6 pt-4 space-y-2">
            <AppAlert v-if="flash.success" variant="success" :message="flash.success" dismissible />
            <AppAlert v-if="flash.error"   variant="danger"  :message="flash.error"   dismissible />
            <AppAlert v-if="flash.warning" variant="warning" :message="flash.warning" dismissible />
        </div>

        <!-- Contenu pleine largeur -->
        <main class="flex-1 w-full px-6 py-6">
            <slot />
        </main>

        <!-- Toast notifications globales -->
        <ToastContainer />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppHeader from '@/Components/Layout/AppHeader.vue';
import AppAlert from '@/Components/UI/AppAlert.vue';
import ToastContainer from '@/Components/UI/ToastContainer.vue';

const page  = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string; warning?: string });
</script>
