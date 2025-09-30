<script setup lang="ts">

import { Card, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button'
import axios from '../axios';
import { Link } from '@inertiajs/vue3';

const emit = defineEmits<{
    (e: 'update'): void; // 'change' event with a string payload
}>();

interface Task {
    title: string
    description?: string
    id: string
    status: boolean
}

defineProps<{
    task: Task
}>()

const deleteTask = async (id: string) => {
    try {
        await axios.delete(`/api/tasks/${id}`)
        emit('update');
    } catch(err){
        console.log(err);
    }
}
</script>

<template>
    <Card :class="{'bg-green-300/20': task.status}">
        <CardHeader>
            <CardTitle>{{task.title}}</CardTitle>
            <CardDescription>{{task.description}}</CardDescription>
        </CardHeader>
        <CardFooter class="flex justify-end gap-2">
            <Link :href="'task/' + task.id">
                <Button>View</Button>
            </Link>
            <Button @click="deleteTask(task.id)" variant="destructive">
                Delete
            </Button>
        </CardFooter>
    </Card>
</template>
