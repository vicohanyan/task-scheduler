<template>
  <form @submit.prevent="onSubmit" class="task-form">
    <label>
      Name *
      <input v-model.trim="model.name" required placeholder="Task name" />
    </label>

    <label>
      Description
      <textarea v-model.trim="model.description" rows="3" placeholder="Details..."></textarea>
    </label>

    <label>
      Status
      <select v-model="model.status">
        <option value="todo">To Do</option>
        <option value="in_progress">In Progress</option>
        <option value="done">Done</option>
        <option value="blocked">Blocked</option>
      </select>
    </label>

    <label>
      Priority
      <input type="number" v-model.number="model.priority" min="0" max="100" />
    </label>

    <label>
      Due date
      <input type="date" v-model="model.due_date" />
    </label>

    <label>
      Assignee
      <select v-model.number="model.assigned_user_id">
        <option :value="undefined">— Unassigned —</option>
        <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
      </select>
    </label>

    <div class="actions">
      <button type="submit" class="btn primary">{{ submitText || 'Save' }}</button>
      <button type="button" class="btn" @click="$emit('cancel')">Cancel</button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import type { CreateTaskPayload, Task, User } from '@/api/types';
import { fetchUsers } from '@/api/user';

const props = defineProps<{ initial?: Partial<Task | CreateTaskPayload>; submitText?: string }>();
const emit = defineEmits<{ (e: 'submit', payload: CreateTaskPayload): void; (e: 'cancel'): void }>();

const users = ref<User[]>([]);

const model = reactive<CreateTaskPayload>({
  name: props.initial?.name ?? '',
  description: props.initial?.description ?? '',
  status: (props.initial as Task)?.status ?? 'todo',
  priority: (props.initial as Task)?.priority ?? 0,
  due_date: props.initial?.due_date ?? '',
  assigned_user_id: props.initial?.assigned_user_id ?? undefined,
});

onMounted(async () => {
  users.value = await fetchUsers();
});

function onSubmit() {
  emit('submit', { ...model });
};
</script>

<style scoped>
.task-form { display: flex; flex-direction: column; gap: 12px; max-width: 400px; margin: auto; }
label { display: flex; flex-direction: column; gap: 4px; }
.actions { display: flex; gap: 8px; justify-content: flex-end; }
.btn { padding: 6px 12px; border-radius: 6px; border: 1px solid #ccc; background: #eee; cursor: pointer; }
.btn.primary { background: #2563eb; color: #fff; border: none; }
</style>
