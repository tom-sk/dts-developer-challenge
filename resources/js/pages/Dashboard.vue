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

const tasks = ref([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Form fields and error messages
const newTaskTitle = ref('');
const newTaskDescription = ref('');
const newTaskDue = ref('');
const newTaskStatus = ref(false);
const formErrors = ref<{ [key: string]: string[] }>({});

const getTasks = async () => {
    try {
        const response = await axios.get('/api/tasks');
        tasks.value = response.data.data;
    } catch (err: any) {
        error.value = err;
    } finally {
        loading.value = false;
    }
};

const createTask = async () => {
    formErrors.value = {};
    try {
        const response = await axios.post('/api/tasks', {
            title: newTaskTitle.value,
            description: newTaskDescription.value,
            due: newTaskDue.value,
            status: newTaskStatus.value,
        });

        // Clear form and refresh tasks
        newTaskTitle.value = '';
        newTaskDescription.value = '';
        newTaskDue.value = '';
        newTaskStatus.value = false;

        tasks.value.unshift(response.data.data);
    } catch (err: any) {
        if (err.response?.status === 422) {
            formErrors.value = err.response.data.errors;
        } else {
            error.value = err;
        }
    }
};

onMounted(() => {
    getTasks();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Create Task Form -->
            <div class="mb-4 rounded border p-4 shadow-sm">
                <h2 class="text-lg font-semibold mb-2">Create New Task</h2>
                <div class="flex flex-col gap-2">
                    <input
                        type="text"
                        placeholder="Title"
                        v-model="newTaskTitle"
                        class="border p-2 rounded"
                    />
                    <p v-if="formErrors.title" class="text-red-500 text-sm">
                        {{ formErrors.title.join(', ') }}
                    </p>

                    <textarea
                        placeholder="Description"
                        v-model="newTaskDescription"
                        class="border p-2 rounded"
                    ></textarea>
                    <p v-if="formErrors.description" class="text-red-500 text-sm">
                        {{ formErrors.description.join(', ') }}
                    </p>

                    <input
                        type="date"
                        v-model="newTaskDue"
                        class="border p-2 rounded"
                    />
                    <p v-if="formErrors.due" class="text-red-500 text-sm">
                        {{ formErrors.due.join(', ') }}
                    </p>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="newTaskStatus" />
                        Completed
                    </label>

                    <button
                        @click="createTask"
                        class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700"
                    >
                        Create Task
                    </button>
                </div>
            </div>

            <!-- Task List -->
            <div>
                <p v-if="loading">Loading...</p>
                <p v-else-if="error">Error: {{ error.message }}</p>
                <div class="grid grid-col-1 gap-4">
                    <div v-for="task in tasks" :key="task.id">
                        <TaskItem @update="getTasks" :task="task" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
