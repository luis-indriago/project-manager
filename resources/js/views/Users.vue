<template>
  <Sidebar>
    <div class="p-6">
       <h1 class="text-3xl font-bold mb-6">Usuarios</h1>
    
      <!-- Buscador -->
      <div class="flex gap-2 mb-4 justify-between">
        <input
          v-model="search"
          @input="onSearch"
          type="text"
          placeholder="Buscar por nombre o email"
          class="border px-3 py-2 rounded w-full md:w-1/3"
        />

        <button
          @click="openCreate"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          + Nuevo usuario
        </button>
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white border">
          <thead>
            <tr class="bg-gray-100 text-left text-sm uppercase">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Nombre</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Rol</th>
              <th class="px-4 py-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="u in users"
              :key="u.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ u.id }}</td>
              <td class="px-4 py-2">{{ u.name }}</td>
              <td class="px-4 py-2">{{ u.email }}</td>
              <td class="px-4 py-2">
                {{ u.roles?.map(r => r.name).join(', ') }}
              </td>
              <td class="px-4 py-2 flex gap-2">
                <button
                  @click="openEdit(u)"
                  class="text-blue-600 hover:underline"
                >
                  Editar
                </button>
                <button
                  @click="confirmDelete(u)"
                  class="text-red-600 hover:underline"
                >
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="users.length === 0">
              <td colspan="5" class="text-center px-4 py-6 text-gray-500">
                Sin usuarios
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="flex justify-between items-center mt-4">
        <button
          :disabled="meta.current_page === 1"
          @click="goToPage(meta.current_page - 1)"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Anterior
        </button>

        <span class="text-sm text-gray-700">
          Página {{ meta.current_page }} de {{ meta.last_page }} (Total: {{ meta.total }})
        </span>

        <button
          :disabled="meta.current_page === meta.last_page"
          @click="goToPage(meta.current_page + 1)"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Siguiente
        </button>
      </div>
    </div>

    <!-- Modal Crear/Editar -->
    <Modal :show="showModal" @close="closeModal">
      <h2 class="text-lg font-bold mb-4">
        {{ editing ? 'Editar usuario' : 'Crear usuario' }}
      </h2>
      <UserForm
        :user="selectedUser"
        @saved="onSaved"
        @cancel="closeModal"
      />
    </Modal>

    <!-- Modal Confirmar eliminación -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <h2 class="text-lg font-bold mb-4">¿Eliminar usuario?</h2>
      <p class="mb-4">Esta acción no se puede deshacer.</p>
      <div class="flex justify-end gap-2">
        <button
          @click="showDeleteModal = false"
          class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
        >
          Cancelar
        </button>
        <button
          @click="deleteUser"
          class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700"
        >
          Eliminar
        </button>
      </div>
    </Modal>
  </Sidebar>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import Modal from '@/components/Modal.vue'
import UserForm from '@/components/UserForm.vue'
import { useUserStore } from '@/stores/users'

const userStore = useUserStore()

const users = ref([])
const search = ref('')
const page = ref(1)
const meta = ref({})

const showModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)
const editing = ref(false)

onMounted(() => loadUsers())

async function loadUsers() {
  await userStore.fetchUsers({ page: page.value, search: search.value })
  users.value = userStore.users
  meta.value = userStore.meta
}

function onSearch() {
  page.value = 1
  loadUsers()
}

function goToPage(p) {
  page.value = p
  loadUsers()
}

function openCreate() {
  selectedUser.value = null
  editing.value = false
  showModal.value = true
}

function openEdit(u) {
  selectedUser.value = { ...u }
  editing.value = true
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedUser.value = null
}

async function onSaved() {
  closeModal()
  await loadUsers()
}

function confirmDelete(u) {
  selectedUser.value = u
  showDeleteModal.value = true
}

async function deleteUser() {
  await userStore.deleteUser(selectedUser.value.id)
  showDeleteModal.value = false
  await loadUsers()
}
</script>