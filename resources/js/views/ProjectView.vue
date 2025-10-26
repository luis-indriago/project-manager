<template>
  <Sidebar>
    <div class="p-6">

      <Modal :show="showTaskModal" @close="showTaskModal = false">
        <h2 class="text-lg font-bold mb-4">
          {{ taskToEdit ? 'Editar tarea' : 'Crear tarea' }}
        </h2>
        <TaskForm
          :task="taskToEdit"
          :project-id="project.id"
          :users="users || []"
          @saved="onTaskSaved"
        />
      </Modal>

      <h1 class="text-3xl font-bold mb-2">{{ project.name }}</h1>
      <p class="text-gray-700 mb-6">{{ project.description }}</p>

      <!-- Barra de filtros -->
      <div class="flex items-center gap-3 mb-6 h-10">
        <select v-model="filter.status" class="border px-3 rounded h-full bg-white">
          <option value="">Todos los estados</option>
          <option value="pending">Pendientes</option>
          <option value="in_progress">En proceso</option>
          <option value="completed">Completadas</option>
        </select>

        <select v-model="filter.userId" class="border px-3 rounded h-full bg-white">
          <option value="">Todos los usuarios</option>
          <option v-for="u in users" :key="u.id" :value="u.id">
            {{ u.name }}
          </option>
        </select>

        <button @click="clearFilters" class="text-sm text-gray-600 underline self-center">
          Limpiar filtros
        </button>

        <button
          @click="openCreateTask"
          class="ml-auto bg-green-600 text-white px-4 rounded h-full hover:bg-green-700"
        >
          + Nueva tarea
        </button>
      </div>

      <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
          <span>Progreso del proyecto:</span>
          <span>{{ projectProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 h-2 rounded-full">
          <div
            class="bg-blue-500 h-2 rounded-full"
            :style="{ width: projectProgress + '%' }"
          ></div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- PENDIENTES -->
        <Column
          title="Pendientes"
          status="pending"
          :tasks="filteredPending"
          @drop="handleDrop"
          @deleteTask="confirmDelete"
          @edit-task="openEditTask"
        />

        <!-- EN PROCESO -->
        <Column
          title="En proceso"
          status="in_progress"
          :tasks="filteredProgress"
          @drop="handleDrop"
          @deleteTask="confirmDelete"
          @edit-task="openEditTask"
        />

        <!-- COMPLETADAS -->
        <Column
          title="Completadas"
          status="completed"
          :tasks="filteredCompleted"
          @drop="handleDrop"
          @deleteTask="confirmDelete"
          @edit-task="openEditTask"
        />
      </div>
    </div>

    <!-- Modal borrar -->
    <Modal :show="showModal" @close="showModal = false" >
      <h2 class="text-lg font-semibold mb-4">¿Eliminar tarea?</h2>
      <div class="flex justify-end gap-2">
        <button
          class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
          @click="showModal = false"
        >
          Cancelar
        </button>
        <button
          class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700"
          @click="deleteTask"
        >
          Eliminar
        </button>
      </div>
    </Modal>
  </Sidebar>
</template>

<script setup>
/* ---------- imports ---------- */
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from '@/components/Sidebar.vue'
import Modal from '@/components/Modal.vue'
import TaskForm from '@/components/TaskForm.vue'
import Column from '@/components/Column.vue'
import { useProjectStore } from '@/stores/projects'
import { useUserStore } from '@/stores/users'


/* ---------- projectStore ---------- */
const route = useRoute()
const projectStore = useProjectStore()
const userStore = useUserStore()


/* ---------- estado local ---------- */
const project = ref({})
const users = ref([])
const tasksPending   = ref([])
const tasksInProgress = ref([])
const tasksCompleted  = ref([])

const filter = ref({
  status: '',
  userId: '',
})

const showModal = ref(false)
const taskToDelete = ref(null)
const showTaskModal = ref(false)
const taskToEdit = ref(null)

const filteredPending   = computed(() => filteredTasks.value.filter(t => t.status === 'pending'))
const filteredProgress  = computed(() => filteredTasks.value.filter(t => t.status === 'in_progress'))
const filteredCompleted = computed(() => filteredTasks.value.filter(t => t.status === 'completed'))

/* ---------- carga inicial ---------- */
onMounted(async () => {
  const id = route.params.id
  project.value = await projectStore.fetchProjectDetail(id)
  splitTasks(project.value.tasks)
  users.value = await userStore.fetchAllUsers()
})



/* ---------- dividir tareas ---------- */
function splitTasks(list = []) {
  tasksPending.value   = list.filter(t => t.status === 'pending')
  tasksInProgress.value = list.filter(t => t.status === 'in_progress')
  tasksCompleted.value  = list.filter(t => t.status === 'completed')
}

/* ---------- mover entre columnas ---------- */
function handleDrop({ task, newStatus }) {
  // 1. quitar de la lista anterior
  tasksPending.value   = tasksPending.value.filter(t => t.id !== task.id)
  tasksInProgress.value = tasksInProgress.value.filter(t => t.id !== task.id)
  tasksCompleted.value  = tasksCompleted.value.filter(t => t.id !== task.id)

  // 2. cambiar status
  task.status = newStatus

  // 3. meter en la nueva
  if (newStatus === 'pending')   tasksPending.value.push(task)
  if (newStatus === 'in_progress') tasksInProgress.value.push(task)
  if (newStatus === 'completed')  tasksCompleted.value.push(task)

  // 4. persistir
  projectStore.updateTaskStatusOnly(task.id, newStatus)
}

/* ---------- borrado ---------- */
function confirmDelete(task) {
  taskToDelete.value = task 
  showModal.value = true
}

async function deleteTask() {
  
  if (!taskToDelete.value) {
    return
  }
  
  await projectStore.deleteTask(taskToDelete.value.id)
  splitTasks([
    ...tasksPending.value,
    ...tasksInProgress.value,
    ...tasksCompleted.value
  ].filter(t => t.id !== taskToDelete.value.id))
  showModal.value = false
  taskToDelete.value = null
}

/* ---------- crear ---------- */
function openCreateTask() {
  taskToEdit.value = null
  showTaskModal.value = true
}

function openEditTask(task) {
  taskToEdit.value = task
  showTaskModal.value = true
}

async function onTaskSaved() {
  showTaskModal.value = false
  await reloadProject()
}

async function reloadProject() {
  project.value = await projectStore.fetchProjectDetail(route.params.id)
  splitTasks(project.value.tasks)
}

const filteredTasks = computed(() => {
  const tasks = [
    ...tasksPending.value,
    ...tasksInProgress.value,
    ...tasksCompleted.value,
  ]
  return tasks.filter(t => {
    if (filter.value.status && t.status !== filter.value.status) return false
    if (filter.value.userId && t.assigned_user?.id != filter.value.userId) return false
    return true
  })
})

function clearFilters() {
  filter.value = { status: '', projectId: '', userId: '' }
}

const projectProgress = computed(() => {
  const all = [
    ...tasksPending.value,
    ...tasksInProgress.value,
    ...tasksCompleted.value,
  ]
  if (!all.length) return 0

  const total = all.length
  const completed = all.filter(t => t.status === 'completed').length
  return Math.round((completed / total) * 100)
})

</script>