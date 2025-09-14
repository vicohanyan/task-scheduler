<template>
  <div>
    <div class="create_task_page">
      <h2>Create Task</h2>
      <TaskForm submitText="Create" @submit="onCreate" @cancel="goBack" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import TaskForm from '@/components/tasks/TaskForm.vue';
import { createTask } from '@/api/task';
import type { CreateTaskPayload } from '@/api/types';

const router = useRouter();
async function onCreate(payload: CreateTaskPayload) {
  try {
    const t = await createTask(payload);
    router.push(`/tasks/${t.id}/show`);
  } catch (e) {
    console.error('Create failed', e);
  }
}

function goBack() {
  router.push('/tasks-list');
}
</script>
