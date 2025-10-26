<template>
  <Teleport to="body">
    <transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center"
      >
        <div
          class="absolute inset-0 bg-black opacity-50"
          @click="close"
        ></div>

        <div
          class="relative bg-white rounded-lg shadow-lg w-full max-w-lg p-6 z-10"
        >
          <button
            @click="close"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
          >
            ✕
          </button>

          <slot />
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
})
const emit = defineEmits(['close'])

const close = () => emit('close')
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
