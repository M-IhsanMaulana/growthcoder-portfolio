export const useFetchAPI = <T>(url: string | (() => string), options: any = {}) => {
  const config = useRuntimeConfig()

  const headers = {
    'Accept': 'application/json',
    'X-API-Key': config.public.apiKey,
    ...options.headers,
  }

  return useFetch<T>(url, {
    baseURL: config.public.apiBase,
    ...options,
    headers,
  })
}
