import { defineEventHandler, setHeader, createError } from 'h3'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()

  try {
    // 1. Fetch settings from the Laravel API
    const settingsRes = await $fetch<any>(`${config.public.apiBase}/settings`, {
      headers: {
        'Accept': 'application/json',
        'X-API-Key': config.public.apiKey
      }
    })

    const cvUrl = settingsRes?.data?.cv_file_url

    if (!cvUrl) {
      throw createError({
        statusCode: 404,
        statusMessage: 'CV file is not uploaded or set in site settings.'
      })
    }

    // Rewrite cvUrl to match the origin of apiBase to avoid SSL/port issues in local environment
    let fetchUrl = cvUrl
    try {
      const apiOrigin = new URL(config.public.apiBase).origin
      const cvUrlObj = new URL(cvUrl)
      fetchUrl = `${apiOrigin}${cvUrlObj.pathname}`
    } catch (e) {
      console.warn('Could not rewrite CV URL origin, using original:', e)
    }

    // 2. Fetch the actual PDF content from the CMS
    const pdfData = await $fetch<ArrayBuffer>(fetchUrl, {
      responseType: 'arrayBuffer'
    })

    // 3. Set correct headers for inline viewing
    setHeader(event, 'Content-Type', 'application/pdf')
    setHeader(event, 'Content-Disposition', 'inline; filename="CV-Ihsan.pdf"')

    return Buffer.from(pdfData)
  } catch (err: any) {
    console.error('Error serving CV:', err)
    throw createError({
      statusCode: err.statusCode || 500,
      statusMessage: `Failed to retrieve CV: ${err.message || err.statusMessage || err}`
    })
  }
})
