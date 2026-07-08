import { ref } from 'vue'

export function useXRayPreview(options = {}) {
  const endpoint = options.endpoint || '/api/x/v1/pay-codes/x-ray'
  const loading = ref(false)
  const error = ref(null)
  const result = ref(null)

  async function inspect(code, payload = {}) {
    if (!code) {
      result.value = null
      error.value = null
      return null
    }

    loading.value = true
    error.value = null

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(options.headers || {}),
        },
        body: JSON.stringify({ code, channel: payload.channel || 'claim', ...payload }),
      })

      const json = await response.json()

      if (!response.ok || json.success === false) {
        throw new Error(json.message || 'Unable to inspect this Pay Code.')
      }

      result.value = json.data?.xray || json.data || json
      return result.value
    } catch (e) {
      error.value = e?.message || 'Unable to inspect this Pay Code.'
      result.value = null
      return null
    } finally {
      loading.value = false
    }
  }

  function clear() {
    result.value = null
    error.value = null
    loading.value = false
  }

  return { loading, error, result, inspect, clear }
}
