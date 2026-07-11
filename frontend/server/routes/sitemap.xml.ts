import { defineEventHandler, setHeader } from 'h3'

export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const baseDomain = 'https://growthcoder.id'

  // Fetch blogs & projects & categories
  let posts: any[] = []
  let projects: any[] = []
  let categories: any[] = []

  try {
    const postsRes = await $fetch<any>(`${config.public.apiBase}/posts?per_page=1000`, {
      headers: {
        'Accept': 'application/json',
        'X-API-Key': config.public.apiKey
      }
    })
    if (postsRes && postsRes.data) {
      posts = postsRes.data
    }
  } catch (err) {
    console.error('Sitemap: Failed to fetch posts:', err)
  }

  try {
    const projectsRes = await $fetch<any>(`${config.public.apiBase}/projects`, {
      headers: {
        'Accept': 'application/json',
        'X-API-Key': config.public.apiKey
      }
    })
    if (projectsRes && projectsRes.data) {
      projects = projectsRes.data
    }
  } catch (err) {
    console.error('Sitemap: Failed to fetch projects:', err)
  }

  try {
    const categoriesRes = await $fetch<any>(`${config.public.apiBase}/blog-categories`, {
      headers: {
        'Accept': 'application/json',
        'X-API-Key': config.public.apiKey
      }
    })
    if (categoriesRes && categoriesRes.data) {
      categories = categoriesRes.data
    }
  } catch (err) {
    console.error('Sitemap: Failed to fetch categories:', err)
  }

  // Construct XML
  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n'
  xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'

  // Static Pages
  const staticRoutes = [
    { path: '/', changefreq: 'daily', priority: '1.0' },
    { path: '/about', changefreq: 'monthly', priority: '0.8' },
    { path: '/about/skills', changefreq: 'monthly', priority: '0.7' },
    { path: '/about/experience', changefreq: 'monthly', priority: '0.7' },
    { path: '/about/education', changefreq: 'monthly', priority: '0.7' },
    { path: '/services', changefreq: 'monthly', priority: '0.8' },
    { path: '/proyek', changefreq: 'weekly', priority: '0.9' },
    { path: '/blog', changefreq: 'daily', priority: '0.9' },
    { path: '/contact', changefreq: 'monthly', priority: '0.8' }
  ]

  for (const route of staticRoutes) {
    xml += '  <url>\n'
    xml += `    <loc>${baseDomain}${route.path}</loc>\n`
    xml += `    <changefreq>${route.changefreq}</changefreq>\n`
    xml += `    <priority>${route.priority}</priority>\n`
    xml += '  </url>\n'
  }

  // Dynamic Projects
  for (const project of projects) {
    if (project.slug) {
      xml += '  <url>\n'
      xml += `    <loc>${baseDomain}/proyek/${project.slug}</loc>\n`
      xml += '    <changefreq>monthly</changefreq>\n'
      xml += '    <priority>0.8</priority>\n'
      if (project.published_at || project.updated_at) {
        const lastmod = new Date(project.updated_at || project.published_at).toISOString().split('T')[0]
        xml += `    <lastmod>${lastmod}</lastmod>\n`
      }
      xml += '  </url>\n'
    }
  }

  // Dynamic Posts
  for (const post of posts) {
    if (post.slug) {
      xml += '  <url>\n'
      xml += `    <loc>${baseDomain}/blog/${post.slug}</loc>\n`
      xml += '    <changefreq>monthly</changefreq>\n'
      xml += '    <priority>0.8</priority>\n'
      if (post.published_at || post.updated_at) {
        const lastmod = new Date(post.updated_at || post.published_at).toISOString().split('T')[0]
        xml += `    <lastmod>${lastmod}</lastmod>\n`
      }
      xml += '  </url>\n'
    }
  }

  // Dynamic Categories
  for (const cat of categories) {
    if (cat.slug && cat.posts_count > 0) {
      xml += '  <url>\n'
      xml += `    <loc>${baseDomain}/blog/kategori/${cat.slug}</loc>\n`
      xml += '    <changefreq>weekly</changefreq>\n'
      xml += '    <priority>0.7</priority>\n'
      xml += '  </url>\n'
    }
  }

  xml += '</urlset>'

  // Set headers and send response
  setHeader(event, 'Content-Type', 'application/xml')
  return xml
})
