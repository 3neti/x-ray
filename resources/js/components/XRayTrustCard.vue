<script setup>
defineProps({
  trust: { type: Object, default: null },
})
</script>

<template>
  <section v-if="trust" class="rounded-2xl border p-4 shadow-sm">
    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Trust & Runtime</p>
    <dl class="mt-3 space-y-2 text-sm text-gray-700">
      <div v-if="trust.version" class="flex justify-between gap-4">
        <dt class="text-gray-500">Version</dt>
        <dd class="font-medium">{{ trust.version }}</dd>
      </div>
      <div v-if="trust.copyright" class="flex justify-between gap-4">
        <dt class="text-gray-500">Copyright</dt>
        <dd class="font-medium text-right">{{ trust.copyright }}</dd>
      </div>
      <div v-if="trust.signatures_enabled !== undefined" class="flex justify-between gap-4">
        <dt class="text-gray-500">Signatures</dt>
        <dd class="font-medium">{{ trust.signatures_enabled ? 'Enabled' : 'Disabled' }}</dd>
      </div>
      <div v-if="trust.public_key_fingerprint" class="space-y-1">
        <dt class="text-gray-500">Public Key Fingerprint</dt>
        <dd class="break-all font-mono text-xs">{{ trust.public_key_fingerprint }}</dd>
      </div>
    </dl>

    <div v-if="trust.licenses" class="mt-3 flex flex-wrap gap-2">
      <span v-for="(license, key) in trust.licenses" :key="key" class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">
        {{ license }}
      </span>
    </div>
  </section>
</template>
