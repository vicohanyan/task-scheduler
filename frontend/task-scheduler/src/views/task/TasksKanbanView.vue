<template>
  <div id="kanban-board">
    <div class="toolbar">
      <RouterLink class="create_task_button" to="/tasks/new">+ Create Task</RouterLink>
      <div class="filters">
        <div>
          <label>Search By Name or Description</label>
          <input
              v-model.trim="search"
              type="text"
              placeholder="Search name or description…"
          />
        </div>
        <div>
          <label>Filter By Assignee</label>
          <select v-model="assignedUserId">
            <option :value="null">All assignees</option>
            <option :value="-1">Unassigned</option>
            <option v-for="u in users" :key="u.id" :value="u.id">
              {{ u.name }}
            </option>
          </select>
        </div>
        <div>
          <button class="btn" @click="apply">Apply</button>
          <button class="btn" @click="reset">Reset</button>
        </div>
      </div>
    </div>
    <div id="kanban-board-block">
      <div
          class="column"
          v-for="col in columns"
          :key="col.code"
      >
        <h3>{{ col.display_name }}</h3>
        <div class="kanban-column">
          <VueDraggable
              class="flex flex-col"
              v-model="col.tasks"
              item-key="id"
              :group="{ name: 'kanban', pull: true, put: true }"
              :animation="150"
              ghost-class="ghost"
              @add="e => onAdd(e, col)"
          >
            <div v-for="task in col.tasks" :key="task.id" class="cursor-move h-30 bg-gray-500/5 rounded p-3">
              <div class="kanban-task">
                <span>Name: {{ task.name }}</span>
                <p>Description: {{ task.description }}</p>
                <p>Due Date: {{ task.due_date ? dayjs(task.due_date).format('DD.MM.YYYY HH:mm') : '—' }}</p>
                <p v-if="task.assignee">Assigned to: {{ task.assignee.name }}</p>
                <p v-else>Assigned to: Unassigned</p>
                <button @click="showTask(task.id)">Show</button>
                <button @click="editTask(task.id)">Edit</button>
                <button @click="removeTask(task.id, col)">Delete</button>
              </div>
            </div>
          </VueDraggable>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {VueDraggable} from 'vue-draggable-plus';
import {fetchTasks, deleteTask, updateTask} from "@/api/task";
import {ref, onMounted, onActivated, watch} from "vue";
import {TASKS_TYPE_KANBAN} from "@/api/types";
import type {KanbanColumn, KanbanTask, TaskStatus} from '@/api/types';
import router from "@/router/index";
import dayjs from "dayjs";
import {fetchUsers} from '@/api/user';


type UIKanbanColumn = Omit<KanbanColumn, 'tasks'> & { tasks: KanbanTask[] };
const columns = ref<UIKanbanColumn[]>([]);

const users = ref<{ id: number; name: string }[]>([]);
const search = ref<string>('');
const status = ref<TaskStatus | undefined>(undefined);
const assignedUserId = ref<number | null>(null);

async function load() {
  const data = await fetchTasks(
      TASKS_TYPE_KANBAN,
      search.value || undefined,
      assignedUserId.value ?? undefined,
      status.value
  );
  const cols = data as KanbanColumn[];
  columns.value = cols.map((c): UIKanbanColumn => ({
    code: c.code,
    display_name: c.display_name,
    tasks: (Array.isArray(c.tasks) ? c.tasks : []).filter(Boolean) as KanbanTask[],
  }));
}

let t: number | undefined
watch([search, status, assignedUserId], () => {
  clearTimeout(t)
  t = window.setTimeout(() => load(), 250)
});

function apply() {
  load();
}

function reset() {
  search.value = '';
  assignedUserId.value = null;
  load();
}

onMounted(async () => {
  try {
    users.value = await fetchUsers();
  } catch {
  }
  await load();
});

onActivated(load);

async function onAdd(e: any, targetCol: UIKanbanColumn) {
  const idx = (e as any).newIndex as number;
  const task = targetCol.tasks[idx];
  if (!task || task.id == null) {
    await load();
    return;
  }
  const prev = task.status;
  const next = targetCol.code;
  if (prev === next) return;

  task.status = next;
  try {
    await updateTask(task.id, {status: next});
  } catch (err) {
    console.error('Failed to update status', err);
    await load();
  }
}

async function removeTask(id: number, col: UIKanbanColumn) {
  try {
    await deleteTask(id);
    const i = col.tasks.findIndex(t => t.id === id);
    if (i !== -1) col.tasks.splice(i, 1);
  } catch (e) {
    console.error('Failed to delete task', e);
  }
}

function editTask(id: number) {
  router.push(`/tasks/${id}/edit`);
}

function showTask(id: number) {
  router.push(`/tasks/${id}/show`);
}

onActivated(load)
onMounted(load)
</script>

<style scoped>
#kanban-board {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.kanban-column, .kanban-column > div {
  height: 100%;
}

#kanban-board-block {
  display: flex;
  gap: 16px;
  flex: 1;
  align-items: stretch;
  overflow-x: auto;
  padding: 12px;
  box-sizing: border-box;
}

.column {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 280px;
  background: #4b4b4b;
  border-radius: 8px;
  overflow: hidden;
}

.column > h3 {
  margin: 0;
  padding: 10px;
  background: #b0b0b0;
  text-align: center;
  color: #1c6b48;
}


.kanban-task {
  border: 1px solid #42b983;
  padding: 10px;
  margin: 6px 0;
  border-radius: 12px;
  background: #2d3748;
}


h2 {
  text-align: center;
}

.toolbar {
  padding: 5px 20px;
}

.create_task_button {
  background: #2d3748;
  padding: 10px;
  margin: 10px;
}

.filters {
  margin-top: 10px;
  width: 100%;
  border: 1px solid #4a5568;
  display: flex;
  justify-content: space-around;

  div {
    display: flex;
    flex-direction: column;

    label {
      color: #1c6b48;
    }
  }

  input, select {
    width: 200px;
    padding: 5px;
    margin: 5px;
  }
}
</style>