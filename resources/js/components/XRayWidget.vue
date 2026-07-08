<script setup>
import { ref } from 'vue'
import { useXRayPreview } from '../composables/useXRayPreview'
import XRayPreview from './XRayPreview.vue'

const props = defineProps({
  endpoint: { type: String, default: '/api/x/v1/pay-codes/x-ray' },
  channel: { type: String, default: 'claim' },
})

const emit = defineEmits(['proceed'])
const code = ref('')
const { loading, error, result, inspect } = useXRayPreview({ endpoint: props.endpoint })

function submit() {
  inspect(code.value, { channel: props.channel })
}
</script>

<template>
  <div class="mx-auto max-w-xl space-y-4">
    <form class="rounded-2xl border p-4 shadow-sm" @submit.prevent="submit">
      <label class="block text-sm font-medium text-gray-700" for="xray-code">Pay Code</label>
      <div class="mt-2 flex gap-2">
        <input id="xray-code" v-model="code" class="min-w-0 flex-1 rounded-xl border px-3 py-2" placeholder="Enter Pay Code" />
        <button class="rounded-xl border px-4 py-2 text-sm font-medium" type="submit">Inspect</button>
      </div>
    </form>

    <XRayPreview :result="result" :loading="loading" :error="error" />

    <button
      v-if="result?.visible"
      class="w-full rounded-xl border px-4 py-3 text-sm font-semibold shadow-sm"
      type="button"
      @click="emit('proceed', { code, result })"
    >
      Proceed to Claim
    </button>
  </div>
</template>
