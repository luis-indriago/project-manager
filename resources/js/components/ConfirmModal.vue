<template>
  <Teleport to="body">
    <transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="cancel"
      >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 text-center">
          <h2 class="text-xl font-semibold mb-4">{{ title }}</h2>
          <p class="text-gray-600 mb-6">{{ message }}</p>

          <div class="flex justify-center gap-4">
            <button
              @click="confirm"
              class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700"
            >
              {{ confirmText }}
            </button>
            <button
              @click="cancel"
              class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400"
            >
              {{ cancelText }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  title: { type: String, default: 'Confirmar acción' },
  message: { type: String, default: '¿Estás seguro?' },
  confirmText: { type: String, default: 'Confirmar' },
  cancelText: { type: String, default: 'Cancelar' },
})
const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => emit('confirm')
const cancel = () => emit('cancel')
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
