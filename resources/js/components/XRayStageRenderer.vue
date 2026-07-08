<script setup>
defineProps({
  stages: { type: Array, default: () => [] },
})
</script>

<template>
  <section v-if="stages.length" class="space-y-3">
    <article v-for="(stage, index) in stages" :key="stage.id || index" class="rounded-2xl border p-4 shadow-sm">
      <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ stage.type || 'Stage' }}</p>

      <div v-if="stage.type === 'image' || stage.payload?.src" class="mt-3">
        <img :src="stage.payload?.src || stage.src" :alt="stage.payload?.alt || stage.alt || ''" class="max-h-64 rounded-xl object-contain" />
      </div>

      <div v-else-if="stage.type === 'link' || stage.payload?.url" class="mt-3">
        <a :href="stage.payload?.url || stage.url" target="_blank" rel="noopener" class="text-sm font-medium underline">
          {{ stage.payload?.label || stage.label || stage.payload?.url || stage.url }}
        </a>
      </div>

      <div v-else class="mt-3 text-sm text-gray-700">
        <p>{{ stage.payload?.message || stage.payload?.body || stage.message || stage.body || stage.title }}</p>
      </div>
    </article>
  </section>
</template>
