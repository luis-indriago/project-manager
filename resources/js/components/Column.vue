<template>
  <div class="bg-gray-50 rounded p-3 h-full">
    <h3 class="font-semibold mb-3">{{ title }}</h3>

    <draggable
      :model-value="tasks"
      group="kanban"
      item-key="id"
      class="min-h-10"
      @change="onChange"
    >
      <template #item="{ element }">
        <TaskCard
          :task="element"
          class="mb-2"
          @delete-task="$emit('deleteTask', $event)"
          @edit-task="$emit('editTask', $event)"
        />
      </template>
    </draggable>
  </div>
</template>

<script setup>
import draggable from 'vuedraggable'
import TaskCard from './TaskCard.vue'

const props = defineProps({
  title: String,
  status: String,   // 'pending' | 'in_progress' | 'completed'
  tasks: Array
})

const emit = defineEmits(['drop', 'deleteTask', 'editTask'])

function onChange(evt) {
  if (evt.added) {
    const task = evt.added.element
    emit('drop', { task, newStatus: props.status })
  }
}
</script>