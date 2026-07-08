<script setup>
defineProps({
  requirements: { type: Array, default: () => [] },
})
</script>

<template>
  <section class="rounded-2xl border p-4 shadow-sm">
    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Claim Requirements</p>

    <ul v-if="requirements.length" class="mt-3 space-y-2">
      <li v-for="item in requirements" :key="item.key || item.label" class="text-sm text-gray-700">
        <span class="font-medium">{{ item.label || item.key }}</span>
        <span v-if="item.description" class="block text-gray-500">{{ item.description }}</span>
      </li>
    </ul>

    <p v-else class="mt-2 text-sm text-gray-500">No public requirements are available for this viewer.</p>
  </section>
</template>
