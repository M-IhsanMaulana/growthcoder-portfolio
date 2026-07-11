export const useSettings = () => {
  const settings = useState<any>('site-settings', () => null)

  const fetchSettings = async () => {
    if (settings.value) return settings.value

    try {
      const { data, error } = await useFetchAPI<any>('/settings')
      if (!error.value && data.value) {
        settings.value = data.value.data
      } else if (error.value) {
        console.error('Error fetching site settings:', error.value)
      }
    } catch (err) {
      console.error('Failed to fetch settings:', err)
    }
    return settings.value
  }

  return {
    settings,
    fetchSettings,
  }
}
