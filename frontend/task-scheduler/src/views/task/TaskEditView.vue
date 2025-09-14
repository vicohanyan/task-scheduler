<template>
  <div>
    <div class="edit_task_page">
      <h2>Edit Task #{{ id }}</h2>
      <TaskForm
          v-if="ready"
          :initial="initial"
          submitText="Save"
          @submit="onSave"
          @cancel="goBack"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import TaskForm from '@/components/tasks/TaskForm.vue';
import { fetchTask, updateTask } from '@/api/task';
import type { CreateTaskPayload, Task } from '@/api/types';

const route = useRoute();
const router = useRouter();
const id = Number(route.params.id);
const initial = ref<Partial<Task>>({});
const ready = ref(false);

onMounted(async () => {
  try {
    const task = await fetchTask(id);
    initial.value = task;
    ready.value = true;
  } catch (e) {
    console.error('Failed to load task', e);
  }
})

async function onSave(payload: CreateTaskPayload) {
  try {
    await updateTask(id, payload);
    router.push(`/tasks/${id}/show`);
  } catch (e) {
    console.error('Update failed', e);
  }
}
function goBack() {
  router.push(`/tasks/${id}/show`);
}
</script>