<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { defineProps, onMounted, ref, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import axios from '@/axios';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
];

const data = ref(null);
const loading = ref(true);
const error = ref(null);

// Get the task from Laravel via Inertia
const props = defineProps<{
    task: { id: number; title: string; description?: string };
}>();


const getTask = async () => {
    const taskId = props.task.data.id;
    try {
        const response = await axios.get(`/api/tasks/${taskId}`);
        data.value = response.data.data;
    } catch (err) {
        error.value = err;
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    getTask()
});

watch(
    () => data.value?.status,
    async (newStatus, oldStatus) => {
        if (newStatus === undefined || newStatus === oldStatus) return;

        try {
            if (!data.value) return;

            // Send full task object to API
            await axios.put(`/api/tasks/${data.value.id}`, {
                ...data.value,
                status: newStatus,
            });
        } catch (err) {
            console.error('Failed to update task:', err);
        }
    },
    { immediate: false }
);
</script>

<template>
    <Head title="Task" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="data" class="p-4">
            <Card :class="{'bg-green-300/20': data.status}" class="transition-all ease">
                <CardHeader>
                    <CardTitle>{{data.title}}</CardTitle>
                </CardHeader>
                <CardContent>
                    {{data.description}}
                </CardContent>

                <CardFooter>
                    <div class="items-top flex gap-x-2">
                        <Checkbox id="status" v-model="data.status" />
                        <div class="grid gap-1.5 leading-none">
                            <label
                                for="terms1"
                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                            >
                                Status
                            </label>
                        </div>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
