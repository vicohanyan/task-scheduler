<template>
  <div class="container">
    <div class="show_task_page" v-if="task">
      <div class="task-header">
        <h2>{{ task.name }}</h2>
        <p>Status: {{ task.status_info?.display_name || '—' }}</p>
      </div>
      <div class="task-description">
        <p>{{ task.description }}</p>
      </div>
      <div class="task-info">
        <p>Priority: {{ task.priority }}</p>
        <p>Due Date: {{ dayjs(task.due_date).format('DD.MM.YYYY HH:mm') }}</p>
        <p>Assignee: {{ task.assignee?.name || 'Unassigned' }}</p>
      </div>
    </div>
    <div v-if="task">
      <button @click="editTask(task.id)">Edit</button>
      <button @click="removeTask(task.id)">Delete</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import {ref, onMounted} from 'vue';
import {useRoute} from 'vue-router';
import router from '@/router';
import {fetchTask, deleteTask} from '@/api/task';
import type {Task} from '@/api/types';
import dayjs from "dayjs";

const route = useRoute();
const id = Number(route.params.id);
const task = ref<Task | null>(null);

onMounted(async () => {
  task.value = await fetchTask(id);
})


function editTask(id: number) {
  router.push(`/tasks/${id}/edit`);
}

function removeTask(id: number) {
  try {
    deleteTask(id);
    router.push('/tasks-list');
  } catch (e) {
    console.error('Failed to delete task', e);
  }
}
</script>
<style>
.container {
  max-width: 1200px;
  width: 100%;
  display: block;
  margin: auto;

  .show_task_page {
    width: 100%;
    padding: 20px;
    background: #4a5568;
    border-radius: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;

    .task-header, .task-description, .task-info {
      display: flex;
      justify-content: space-between;
      width: 100%;
      padding: 10px;
      margin: 5px;
    }

    .task-header {
      background: #1c6b48;
    }
  }
}
</style>