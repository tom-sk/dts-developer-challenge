<script setup lang="ts">
import TaskItem from '@/components/TaskItem.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from '../axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const data = ref(null);
const loading = ref(true);
const error = ref(null);

const getTasks = async () => {
    const response = await axios.get('/api/tasks'); // Replace with your endpoint
    data.value = response.data.data;
}

onMounted(async () => {
    try {
        getTasks()
    } catch (err) {
        error.value = err;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div>
                <p v-if="loading">Loading...</p>
                <p v-else-if="error">Error: {{ error.message }}</p>
                <div class="grid grid-col-1 gap-4">
                    <div v-for="task in data" :key="task.id">
                        <TaskItem @update="getTasks" :task />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
