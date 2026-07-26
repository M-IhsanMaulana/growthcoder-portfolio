import DOMPurify from 'dompurify'

/**
 * Sanitize HTML content to prevent XSS attacks while retaining valid rich text HTML tags and styling.
 */
export const sanitizeHtml = (content: string | null | undefined): string => {
  if (!content) return ''

  if (import.meta.client) {
    return DOMPurify.sanitize(content, {
      ADD_TAGS: ['figure', 'figcaption', 'iframe'],
      ADD_ATTR: ['target', 'allow', 'allowfullscreen', 'frameborder', 'scrolling', 'style', 'class']
    })
  }

  // Safe server-side fallback sanitization
  return content
    .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
    .replace(/\s+on\w+="[^"]*"/gi, '')
    .replace(/\s+on\w+='[^']*'/gi, '')
    .replace(/href="javascript:[^"]*"/gi, 'href="#"')
    .replace(/href='javascript:[^']*'/gi, "href='#'")
}
