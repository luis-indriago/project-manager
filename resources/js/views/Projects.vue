<template>
  <Sidebar>
    <div class="p-6">
      <h1 class="text-3xl font-bold mb-6">Mis Proyectos</h1>

      <!-- Botón crear proyecto -->
      <div class="flex justify-end mb-6">
        <button
          @click="openProjectForm()"
          class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700"
        >
          + Nuevo proyecto
        </button>
      </div>

      <!-- Modal formulario -->
      <Modal :show="showProjectForm" @close="closeProjectForm">
        <h2 class="text-2xl font-semibold mb-4">
          {{ editingProject ? 'Editar Proyecto' : 'Crear Proyecto' }}
        </h2>
        <ProjectForm
          v-if="showProjectForm"
          :key="editingProject ? editingProject.id : 'new'"
          :project="editingProject"
          @saved="onProjectSaved"
        />
      </Modal>

      <!-- Modal confirmar eliminación -->
      <ConfirmModal
        :show="showConfirm"
        title="Eliminar Proyecto"
        message="¿Seguro que deseas eliminar este proyecto? Esta acción no se puede deshacer."
        confirmText="Eliminar"
        cancelText="Cancelar"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />

      <!-- Cargando / vacío -->
      <div v-if="projectStore.loading" class="text-gray-500">Cargando proyectos...</div>
      <div v-else-if="projectStore.projects.length === 0" class="text-gray-500">
        No tienes proyectos aún.
      </div>

      <!-- Grid de tarjetas -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ProjectCard
          v-for="p in projectStore.projects"
          :key="p.id"
          :project="p"
          @view="goToProject"
          @edit="editProject"
          @delete="askDelete"
        />
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import Sidebar from '@/components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import { useProjectStore } from '@/stores/projects'
import ProjectForm from '@/components/ProjectForm.vue'
import ProjectCard from '@/components/ProjectCard.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'
import Modal from '@/components/Modal.vue'
import router from '@/router'

const projectStore = useProjectStore()

const showProjectForm = ref(false)
const editingProject = ref(null)
const showConfirm = ref(false)
const projectToDelete = ref(null)

onMounted(() => projectStore.fetchProjects())

const openProjectForm = (project = null) => {
  editingProject.value = project ? { ...project } : null
  showProjectForm.value = true
}

const closeProjectForm = () => {
  showProjectForm.value = false
  setTimeout(() => (editingProject.value = null), 300)
}

const onProjectSaved = () => {
  closeProjectForm()
  projectStore.fetchProjects()
}

const editProject = (project) => openProjectForm(project)
const askDelete = (id) => {
  projectToDelete.value = id
  showConfirm.value = true
}

const confirmDelete = async () => {
  await projectStore.deleteProject(projectToDelete.value)
  showConfirm.value = false
  projectToDelete.value = null
}

const cancelDelete = () => {
  showConfirm.value = false
  projectToDelete.value = null
}

const goToProject = (id) => router.push(`/projects/${id}`)
</script>