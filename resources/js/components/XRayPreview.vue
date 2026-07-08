<script setup>
import XRayRequirementsCard from './XRayRequirementsCard.vue'
import XRayStageRenderer from './XRayStageRenderer.vue'
import XRayStatusCard from './XRayStatusCard.vue'
import XRayTrustCard from './XRayTrustCard.vue'

defineProps({
  result: { type: Object, default: null },
  loading: { type: Boolean, default: false },
  error: { type: String, default: null },
})
</script>

<template>
  <div class="space-y-4">
    <div v-if="loading" class="rounded-2xl border p-4 text-sm text-gray-600 shadow-sm">
      Inspecting Pay Code…
    </div>

    <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
      {{ error }}
    </div>

    <template v-else-if="result">
      <XRayStatusCard :status="result.status" :visible="result.visible" />

      <section v-if="result.disclosures?.length" class="rounded-2xl border p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Allowed Disclosures</p>
        <dl class="mt-3 space-y-2 text-sm">
          <div v-for="item in result.disclosures" :key="item.key" class="flex justify-between gap-4">
            <dt class="text-gray-500">{{ item.label || item.key }}</dt>
            <dd class="text-right font-medium text-gray-900">{{ item.value }}</dd>
          </div>
        </dl>
      </section>

      <XRayRequirementsCard :requirements="result.requirements || []" />
      <XRayStageRenderer :stages="result.stages || []" />
      <XRayTrustCard :trust="result.trust" />

      <section v-if="result.redactions?.length" class="rounded-2xl border p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Privacy</p>
        <p class="mt-2 text-sm text-gray-600">Some details are intentionally hidden for this viewer.</p>
      </section>
    </template>
  </div>
</template>
