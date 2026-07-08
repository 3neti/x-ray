<script setup>
const props = defineProps({
  status: { type: String, default: 'unknown' },
  visible: { type: Boolean, default: false },
})

const labels = {
  claimable: 'Claimable',
  partially_claimable: 'Partially Claimable',
  redeemed: 'Already Claimed',
  expired: 'Expired',
  not_found: 'Not Found',
  hidden: 'Hidden',
  unknown: 'Unknown',
}
</script>

<template>
  <section class="rounded-2xl border p-4 shadow-sm">
    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pay Code Status</p>
    <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ labels[status] || status }}</h3>
    <p class="mt-2 text-sm text-gray-600">
      <span v-if="visible">This Pay Code can be inspected under the current disclosure policy.</span>
      <span v-else>Details are hidden or unavailable for this viewer.</span>
    </p>
  </section>
</template>
