<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { 
    Search, 
    Copy, 
    Check, 
    Play, 
    RefreshCw, 
    Terminal, 
    Lock, 
    Unlock, 
    AlertCircle, 
    Code, 
    Info 
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    apiKey?: string | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dokumentasi API',
                href: '/admin-cms/api-docs',
            },
        ],
    },
});

interface QueryParam {
    name: string;
    type: string;
    required: boolean;
    default?: string;
    description: string;
}

interface PathParam {
    name: string;
    type: string;
    description: string;
}

interface RequestField {
    name: string;
    type: string;
    required: boolean;
    description: string;
}

interface Endpoint {
    id: string;
    method: 'GET' | 'POST' | 'PUT' | 'DELETE';
    path: string;
    title: string;
    description: string;
    category: 'Settings' | 'Projects' | 'Blog' | 'Services' | 'Resume' | 'Inbox' | 'Skills' | 'Tech Stack';
    isProtected: boolean;
    rateLimit?: string;
    pathParams?: PathParam[];
    queryParams?: QueryParam[];
    requestFields?: RequestField[];
    successResponse: any;
    errorResponse?: any;
}

// Full API endpoints dataset
const endpoints: Endpoint[] = [
    {
        id: 'get-settings',
        method: 'GET',
        path: '/api/v1/settings',
        title: 'Get Global Site Settings',
        description: 'Mengambil data konfigurasi situs global, data pemilik portfolio, link sosial media, data file CV, dan default meta tags untuk SEO.',
        category: 'Settings',
        isProtected: false,
        successResponse: {
            data: {
                id: 1,
                owner_full_name: "Muhammad Ihsan Maulana",
                owner_title: "Full-Stack Developer",
                profile_photo: {
                    id: 3,
                    original_filename: "profile.jpg",
                    alt_text: "Profile Photo",
                    urls: {
                        original: "http://localhost:8000/storage/media/profile.jpg",
                        thumbnail: "http://localhost:8000/storage/media/conversions/profile-thumbnail.jpg"
                    }
                },
                hero_headline: "Crafting High-Performance Web Solutions & Intelligent Automations",
                hero_subheadline: "Specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.",
                hero_cta_text: "Hire Me",
                hero_cta_url: "#contact",
                cv_file_url: "http://localhost:8000/storage/cv/CV-Ihsan.pdf",
                social_linkedin: "https://linkedin.com/in/ihsan",
                social_github: "https://github.com/ihsan",
                social_telegram: "https://t.me/ihsan",
                social_instagram: "https://instagram.com/ihsan",
                social_twitter: "https://twitter.com/ihsan",
                contact_email: "contact@growthcoder.id",
                site_name: "growthcoder.id",
                meta_title_suffix: "Growthcoder | Portfolio",
                default_meta_desc: "Portfolio Muhammad Ihsan Maulana - Full-Stack Developer",
                default_og_image: {
                    id: 5,
                    original_filename: "og-image.png",
                    alt_text: "og-image",
                    urls: {
                        original: "http://localhost:8000/storage/media/og-image.png"
                    }
                }
            }
        }
    },
    {
        id: 'get-projects',
        method: 'GET',
        path: '/api/v1/projects',
        title: 'List Case Studies / Projects',
        description: 'Mengambil daftar studi kasus proyek portfolio terpublikasi. Mendukung pencarian, filter kategori, dan filter proyek unggulan (featured).',
        category: 'Projects',
        isProtected: false,
        queryParams: [
            { name: 'category', type: 'string', required: false, description: 'Filter berdasarkan slug kategori proyek (misal: "web-development").' },
            { name: 'featured', type: 'boolean', required: false, description: 'Filter hanya untuk proyek unggulan. Nilai: "true" atau "false".' }
        ],
        successResponse: {
            data: [
                {
                    id: 1,
                    title: "growthcoder.id Portfolio V3",
                    slug: "growthcoder-portfolio-v3",
                    excerpt: "A high-performance portfolio monorepo built using Laravel 13, Inertia, and Nuxt 4.",
                    is_featured: true,
                    order: 1,
                    category: {
                        id: 1,
                        name: "Web Development",
                        slug: "web-development"
                    },
                    cover_image: {
                        id: 12,
                        urls: {
                            original: "http://localhost:8000/storage/media/portfolio.jpg",
                            thumbnail: "http://localhost:8000/storage/media/conversions/portfolio-thumbnail.jpg"
                        }
                    },
                    technologies: [
                        { id: 1, name: "Laravel", slug: "laravel" },
                        { id: 2, name: "Nuxt.js", slug: "nuxt-js" },
                        { id: 3, name: "Tailwind CSS", slug: "tailwind-css" }
                    ],
                    published_at: "2026-07-06T15:00:00Z"
                }
            ]
        }
    },
    {
        id: 'get-project-detail',
        method: 'GET',
        path: '/api/v1/projects/{slug}',
        title: 'Project Detail',
        description: 'Mengambil detail lengkap satu proyek portfolio beserta galeri gambar, deskripsi rich text, dan tech stack yang digunakan.',
        category: 'Projects',
        isProtected: false,
        pathParams: [
            { name: 'slug', type: 'string', description: 'Slug unik dari proyek (misal: "growthcoder-portfolio-v3").' }
        ],
        successResponse: {
            data: {
                id: 1,
                title: "growthcoder.id Portfolio V3",
                slug: "growthcoder-portfolio-v3",
                excerpt: "A high-performance portfolio monorepo built using Laravel 13, Inertia, and Nuxt 4.",
                description: "<p>Detailed HTML content about this awesome case study project...</p>",
                is_featured: true,
                live_preview_url: "https://growthcoder.id",
                github_url: "https://github.com/growthcoder/portfolio",
                category: {
                    id: 1,
                    name: "Web Development",
                    slug: "web-development"
                },
                cover_image: {
                    id: 12,
                    urls: {
                        original: "http://localhost:8000/storage/media/portfolio.jpg"
                    }
                },
                gallery_images: [
                    {
                        id: 13,
                        urls: {
                            original: "http://localhost:8000/storage/media/portfolio-gallery-1.jpg"
                        }
                    }
                ],
                technologies: [
                    { id: 1, name: "Laravel", slug: "laravel" },
                    { id: 2, name: "Nuxt.js", slug: "nuxt-js" }
                ],
                published_at: "2026-07-06T15:00:00Z"
            }
        }
    },
    {
        id: 'get-project-categories',
        method: 'GET',
        path: '/api/v1/project-categories',
        title: 'Get Project Categories',
        description: 'Mendapatkan daftar kategori klasifikasi proyek portfolio.',
        category: 'Projects',
        isProtected: false,
        successResponse: {
            data: [
                {
                    id: 1,
                    name: "Web Development",
                    slug: "web-development",
                    description: "Web application development services."
                }
            ]
        }
    },
    {
        id: 'get-technologies',
        method: 'GET',
        path: '/api/v1/technologies',
        title: 'Get Tech Stack Master Data',
        description: 'Mendapatkan daftar seluruh teknologi (tech stack) master yang digunakan beserta logonya dan status featured.',
        category: 'Tech Stack',
        isProtected: false,
        successResponse: {
            data: [
                {
                    id: 1,
                    name: "Laravel",
                    slug: "laravel",
                    is_featured: true,
                    logo_url: "http://localhost:8000/storage/media/laravel-logo.png"
                }
            ]
        }
    },
    {
        id: 'get-skills',
        method: 'GET',
        path: '/api/v1/skills',
        title: 'Get Skills Listing',
        description: 'Mendapatkan daftar skill yang terbagi per kategori teknologi, lengkap dengan persentase keahlian dan lama tahun pengalaman.',
        category: 'Skills',
        isProtected: false,
        successResponse: {
            data: [
                {
                    id: 1,
                    name: "Backend Development",
                    skills: [
                        {
                            id: 1,
                            name: "Laravel Framework",
                            proficiency_level: 95,
                            years_experience: 5,
                            technology: {
                                id: 1,
                                name: "Laravel",
                                slug: "laravel",
                                logo_url: "http://localhost:8000/storage/media/laravel-logo.png"
                            }
                        }
                    ]
                }
            ]
        }
    },
    {
        id: 'get-posts',
        method: 'GET',
        path: '/api/v1/posts',
        title: 'List Blog Posts',
        description: 'Mengambil daftar artikel blog terpublikasi untuk feed frontend. Mendukung pencarian kata kunci, filter kategori, dan pagination.',
        category: 'Blog',
        isProtected: false,
        queryParams: [
            { name: 'q', type: 'string', required: false, description: 'Pencarian berdasarkan judul atau excerpt artikel.' },
            { name: 'category', type: 'string', required: false, description: 'Slug kategori artikel blog.' },
            { name: 'per_page', type: 'integer', required: false, default: '10', description: 'Jumlah artikel per halaman.' }
        ],
        successResponse: {
            data: [
                {
                    id: 1,
                    title: "Tips Optimasi Next.js Performance",
                    slug: "tips-optimasi-nextjs-performance",
                    excerpt: "Bagaimana cara mendongkrak skor Core Web Vitals pada aplikasi Next.js Anda.",
                    reading_time: 5,
                    published_at: "2026-07-06T15:00:00Z",
                    cover_image: {
                        id: 15,
                        urls: {
                            original: "http://localhost:8000/storage/media/blog-cover.jpg"
                        }
                    },
                    categories: [
                        { id: 2, name: "Web Development", slug: "web-development" }
                    ]
                }
            ],
            links: {
                first: "http://localhost:8000/api/v1/posts?page=1",
                last: "http://localhost:8000/api/v1/posts?page=1",
                prev: null,
                next: null
            },
            meta: {
                current_page: 1,
                from: 1,
                last_page: 1,
                per_page: 10,
                to: 1,
                total: 1
            }
        }
    },
    {
        id: 'get-post-detail',
        method: 'GET',
        path: '/api/v1/posts/{slug}',
        title: 'Blog Post Detail & View Tracker',
        description: 'Mengambil konten lengkap artikel blog beserta meta SEO overrides, daftar artikel terkait, dan otomatis mencatat data statistik view secara aman.',
        category: 'Blog',
        isProtected: false,
        pathParams: [
            { name: 'slug', type: 'string', description: 'Slug unik dari artikel (misal: "tips-optimasi-nextjs-performance").' }
        ],
        successResponse: {
            data: {
                id: 1,
                title: "Tips Optimasi Next.js Performance",
                slug: "tips-optimasi-nextjs-performance",
                excerpt: "Bagaimana cara mendongkrak skor Core Web Vitals pada aplikasi Next.js Anda.",
                content: "<p>Artikel konten dalam format HTML rich text...</p>",
                reading_time: 5,
                meta_title: "Panduan Optimasi Next.js Performance",
                meta_description: "Tingkatkan nilai Core Web Vitals Anda dengan 5 cara praktis berikut.",
                published_at: "2026-07-06T15:00:00Z",
                cover_image: {
                    id: 15,
                    urls: {
                        original: "http://localhost:8000/storage/media/blog-cover.jpg"
                    }
                },
                categories: [
                    { id: 2, name: "Web Development", slug: "web-development" }
                ],
                related_posts: [
                    {
                        id: 2,
                        title: "Mengenal Nuxt 4 Nitro Engine",
                        slug: "mengenal-nuxt-4-nitro-engine",
                        excerpt: "Menilik fitur terbaru web framework Nuxt 4 yang lebih cepat.",
                        reading_time: 4,
                        cover_image: {
                            id: 16,
                            urls: {
                                original: "http://localhost:8000/storage/media/nuxt-cover.jpg"
                            }
                        }
                    }
                ]
            }
        }
    },
    {
        id: 'get-services',
        method: 'GET',
        path: '/api/v1/services',
        title: 'Get Services List',
        description: 'Mendapatkan daftar layanan jasa profesional yang ditawarkan.',
        category: 'Services',
        isProtected: false,
        successResponse: {
            data: [
                {
                    id: 1,
                    title: "Web App Development",
                    description: "Building fast, SEO-friendly, responsive custom web applications.",
                    price_starts_from: 10000000,
                    is_active: true
                }
            ]
        }
    },
    {
        id: 'get-experiences',
        method: 'GET',
        path: '/api/v1/experiences',
        title: 'Get Work Resume / Experiences',
        description: 'Mendapatkan daftar riwayat pengalaman karir secara kronologis beserta logo perusahaan.',
        category: 'Resume',
        isProtected: false,
        successResponse: {
            data: [
                {
                    id: 1,
                    company_name: "Tech Solutions Inc.",
                    role: "Senior Software Engineer",
                    start_date: "2023-01-01",
                    end_date: null,
                    is_current: true,
                    description: "Developing Laravel & Vue apps.",
                    company_logo_url: "http://localhost:8000/storage/media/logo-tech.png"
                }
            ]
        }
    },
    {
        id: 'get-educations',
        method: 'GET',
        path: '/api/v1/educations',
        title: 'Get Education History',
        description: 'Mendapatkan daftar riwayat pendidikan secara kronologis beserta logo institusi.',
        category: 'Resume',
        isProtected: false,
        successResponse: {
            data: [
                {
                    id: 1,
                    institution_name: "Universitas Indonesia",
                    degree: "Bachelor of Computer Science",
                    start_date: "2018-09-01",
                    end_date: "2022-07-31",
                    is_current: false,
                    description: "GPA 3.85 / 4.00",
                    logo_url: "http://localhost:8000/storage/media/logo-ui.png"
                }
            ]
        }
    },
    {
        id: 'post-contact',
        method: 'POST',
        path: '/api/v1/contact',
        title: 'Submit Contact Message',
        description: 'Mengirim pesan formulir kontak dari frontend. Mengharuskan data tervalidasi dan menyertakan honeypot spam detector. Pesan otomatis diteruskan ke Notifikasi Bot Telegram pribadi.',
        category: 'Inbox',
        isProtected: false,
        rateLimit: 'Maksimum 5 request per 60 detik per IP.',
        requestFields: [
            { name: 'name', type: 'string', required: true, description: 'Nama pengirim (maks. 255 karakter).' },
            { name: 'email', type: 'string (email)', required: true, description: 'Alamat email pengirim.' },
            { name: 'subject', type: 'string', required: false, description: 'Subjek atau judul pesan (maks. 255 karakter).' },
            { name: 'message', type: 'string', required: true, description: 'Isi pesan teks (maks. 5000 karakter).' },
            { name: 'website', type: 'string', required: false, description: 'Honeypot field. Wajib dikirimkan namun nilainya HARUS kosong ("") agar tidak dideteksi sebagai bot spam.' }
        ],
        successResponse: {
            message: "Pesan Anda berhasil terkirim! Saya akan segera menghubungi Anda."
        },
        errorResponse: {
            message: "The website field is prohibited.",
            errors: {
                website: [
                    "The website field is prohibited."
                ]
            }
        }
    }
];

// Active selection states
const selectedEndpointId = ref(endpoints[0].id);
const selectedEndpoint = computed(() => {
    const ep = endpoints.find(e => e.id === selectedEndpointId.value) || endpoints[0];
    return { ...ep, isProtected: true };
});

// Search & Filter state
const searchQuery = ref('');
const activeCategoryFilter = ref<'All' | Endpoint['category']>('All');

const filteredEndpoints = computed(() => {
    return endpoints.filter(e => {
        const matchesSearch = e.path.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                              e.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                              e.description.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = activeCategoryFilter.value === 'All' || e.category === activeCategoryFilter.value;
        return matchesSearch && matchesCategory;
    });
});

// Category list for filter tabs
const categories: ('All' | Endpoint['category'])[] = [
    'All',
    'Settings',
    'Projects',
    'Blog',
    'Tech Stack',
    'Skills',
    'Services',
    'Resume',
    'Inbox'
];

// Code snippets snippets configuration
const activeCodeTab = ref<'curl' | 'js' | 'nuxt'>('curl');

const generateCodeSnippet = (endpoint: Endpoint, language: 'curl' | 'js' | 'nuxt'): string => {
    const host = window.location.origin;
    let url = `${host}${endpoint.path}`;
    
    // Replace slug placeholders for visual presentation
    if (url.includes('{slug}')) {
        url = url.replace('{slug}', endpoint.id === 'get-post-detail' ? 'tips-optimasi-nextjs-performance' : 'growthcoder-portfolio-v3');
    }

    const apiKeyHeader = props.apiKey ? props.apiKey : 'YOUR_API_KEY';

    if (language === 'curl') {
        if (endpoint.method === 'GET') {
            const hasQuery = endpoint.queryParams && endpoint.queryParams.length > 0;
            const queryStr = hasQuery ? `?${endpoint.queryParams?.[0].name}=${endpoint.queryParams?.[0].default || 'value'}` : '';
            return `curl -X GET "${url}${queryStr}" \\\n  -H "Accept: application/json" \\\n  -H "X-API-Key: ${apiKeyHeader}"`;
        } else {
            const bodyObj: Record<string, any> = {};
            endpoint.requestFields?.forEach(f => {
                if (f.name === 'website') {
                    bodyObj[f.name] = '';
                } else if (f.name === 'email') {
                    bodyObj[f.name] = 'johndoe@example.com';
                } else {
                    bodyObj[f.name] = f.name === 'message' ? 'Halo Ihsan, saya tertarik...' : 'John Doe';
                }
            });
            return `curl -X POST "${url}" \\\n  -H "Content-Type: application/json" \\\n  -H "Accept: application/json" \\\n  -H "X-API-Key: ${apiKeyHeader}" \\\n  -d '${JSON.stringify(bodyObj, null, 2)}'`;
        }
    } else if (language === 'js') {
        if (endpoint.method === 'GET') {
            return `fetch('${url}', {\n  method: 'GET',\n  headers: {\n    'Accept': 'application/json',\n    'X-API-Key': '${apiKeyHeader}'\n  }\n})\n.then(res => res.json())\n.then(data => console.log(data))\n.catch(err => console.error(err));`;
        } else {
            const bodyObj: Record<string, any> = {};
            endpoint.requestFields?.forEach(f => {
                bodyObj[f.name] = f.name === 'website' ? '' : (f.name === 'email' ? 'johndoe@example.com' : '...');
            });
            return `fetch('${url}', {\n  method: 'POST',\n  headers: {\n    'Content-Type': 'application/json',\n    'Accept': 'application/json',\n    'X-API-Key': '${apiKeyHeader}'\n  },\n  body: JSON.stringify(${JSON.stringify(bodyObj, null, 2)})\n})\n.then(res => res.json())\n.then(data => console.log(data))\n.catch(err => console.error(err));`;
        }
    } else { // Nuxt 4 useFetch
        if (endpoint.method === 'GET') {
            const hasQuery = endpoint.queryParams && endpoint.queryParams.length > 0;
            const queryOption = hasQuery ? `,\n  query: { ${endpoint.queryParams?.[0].name}: 'value' }` : '';
            return `// Nuxt 4 - Fetch data with API Key header\nconst { data, error } = await useFetch('${endpoint.path}', {\n  headers: {\n    'X-API-Key': '${apiKeyHeader}'\n  }${queryOption}\n});`;
        } else {
            return `// Nuxt 4 - Send contact message with API Key header\nconst sendMessage = async (formData) => {\n  try {\n    const response = await $fetch('${endpoint.path}', {\n      method: 'POST',\n      headers: {\n        'X-API-Key': '${apiKeyHeader}'\n      },\n      body: {\n        name: formData.name,\n        email: formData.email,\n        subject: formData.subject,\n        message: formData.message,\n        website: '' // honeypot field\n      }\n    });\n    console.log(response.message);\n  } catch (err) {\n    console.error(err.data);\n  }\n};`;
        }
    }
};

// Clipboard state
const copied = ref(false);
const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text).then(() => {
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    });
};

// Interactive 'Try It Out' variables
const tryItOutLoading = ref(false);
const tryItOutParams = ref<Record<string, string>>({});
const tryItOutBody = ref<Record<string, string>>({});
const tryItOutResponse = ref<{ status: number; statusText: string; body: any } | null>(null);

// Reset Try It Out state when changing endpoint
const selectEndpoint = (id: string) => {
    selectedEndpointId.value = id;
    tryItOutResponse.value = null;
    tryItOutParams.value = {};
    tryItOutBody.value = {};
    
    // Seed default values for Try It Out
    const endpoint = selectedEndpoint.value;
    if (endpoint.pathParams) {
        endpoint.pathParams.forEach(p => {
            tryItOutParams.value[p.name] = p.name === 'slug' ? 
                (endpoint.id.includes('post') ? 'tips-optimasi-nextjs-performance' : 'growthcoder-portfolio-v3') : '1';
        });
    }
    if (endpoint.queryParams) {
        endpoint.queryParams.forEach(q => {
            if (q.name === 'category') {
                tryItOutParams.value[q.name] = '';
            } else if (q.name === 'featured') {
                tryItOutParams.value[q.name] = 'true';
            } else if (q.name === 'per_page') {
                tryItOutParams.value[q.name] = '10';
            } else {
                tryItOutParams.value[q.name] = q.default || '';
            }
        });
    }
    if (endpoint.requestFields) {
        endpoint.requestFields.forEach(f => {
            if (f.name === 'website') {
                tryItOutBody.value[f.name] = '';
            } else if (f.name === 'email') {
                tryItOutBody.value[f.name] = 'visitor@growthcoder.id';
            } else if (f.name === 'subject') {
                tryItOutBody.value[f.name] = 'Kerjasama Web Project';
            } else if (f.name === 'message') {
                tryItOutBody.value[f.name] = 'Halo Ihsan, saya ingin mendiskusikan proyek web yang akan dikembangkan menggunakan Nuxt 4.';
            } else {
                tryItOutBody.value[f.name] = 'John Doe';
            }
        });
    }
};

// Fire actual request to the API
const executeTryItOut = async () => {
    tryItOutLoading.value = true;
    tryItOutResponse.value = null;
    
    const endpoint = selectedEndpoint.value;
    let url = endpoint.path;

    // Substitute path params
    if (endpoint.pathParams) {
        endpoint.pathParams.forEach(p => {
            const val = tryItOutParams.value[p.name] || `{${p.name}}`;
            url = url.replace(`{${p.name}}`, val);
        });
    }

    // Build query params
    const queryParts: string[] = [];
    if (endpoint.queryParams) {
        endpoint.queryParams.forEach(q => {
            const val = tryItOutParams.value[q.name];
            if (val !== undefined && val !== '') {
                queryParts.push(`${encodeURIComponent(q.name)}=${encodeURIComponent(val)}`);
            }
        });
    }
    if (queryParts.length > 0) {
        url = `${url}?${queryParts.join('&')}`;
    }

    // Execute Request
    try {
        const headers: Record<string, string> = {
            'Accept': 'application/json',
        };

        if (props.apiKey) {
            headers['X-API-Key'] = props.apiKey;
        }

        if (endpoint.method === 'POST') {
            headers['Content-Type'] = 'application/json';
        }

        const options: RequestInit = {
            method: endpoint.method,
            headers: headers
        };

        if (endpoint.method === 'POST') {
            options.body = JSON.stringify(tryItOutBody.value);
        }

        const res = await fetch(url, options);
        let resBody;
        try {
            resBody = await res.json();
        } catch {
            resBody = await res.text();
        }

        tryItOutResponse.value = {
            status: res.status,
            statusText: res.statusText,
            body: resBody
        };
    } catch (err: any) {
        tryItOutResponse.value = {
            status: 0,
            statusText: 'Network Error',
            body: { error: err.message || 'Gagal terhubung dengan server API backend.' }
        };
    } finally {
        tryItOutLoading.value = false;
    }
};

// Initial seeding of parameters on load
selectEndpoint(endpoints[0].id);
</script>

<template>
    <Head title="Dokumentasi API" />
    
    <div class="h-[calc(100vh-4rem)] flex flex-col bg-background">
        <!-- Top bar search & tabs -->
        <div class="flex-shrink-0 border-b border-border bg-card/60 backdrop-blur-md px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-foreground tracking-tight flex items-center gap-2">
                    <Terminal class="h-5 w-5 text-primary animate-pulse" />
                    Dokumentasi API Integrasi Frontend
                </h1>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Gunakan panduan dan pengujian interaktif ini untuk membangun aplikasi publik Nuxt 4 Anda.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative w-full md:w-64">
                    <Search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Cari endpoint..." 
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary placeholder:text-muted-foreground/75"
                    />
                </div>
            </div>
        </div>

        <!-- Category tabs filter bar -->
        <div class="flex-shrink-0 border-b border-border bg-muted/20 px-6 py-2 overflow-x-auto flex items-center gap-1.5 scrollbar-none">
            <button 
                v-for="cat in categories" 
                :key="cat"
                @click="activeCategoryFilter = cat"
                class="px-3 py-1.5 text-[11px] font-semibold rounded-md transition-all duration-200 cursor-pointer flex-shrink-0"
                :class="activeCategoryFilter === cat ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground hover:bg-muted/80'"
            >
                {{ cat }}
            </button>
        </div>

        <!-- Split-screen dynamic area -->
        <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
            <!-- Left Panel: List & Detail Documentation -->
            <div class="w-full lg:w-3/5 border-r border-border overflow-y-auto p-6 space-y-6">
                <!-- API Key Info Card -->
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 space-y-2 text-xs">
                    <div class="flex items-center gap-2 text-amber-600 font-bold">
                        <Lock class="h-4 w-4" />
                        Autentikasi API Key Aktif
                    </div>
                    <p class="text-muted-foreground leading-relaxed">
                        Seluruh endpoint API publik dilindungi menggunakan middleware <code>VerifyApiKey</code>. 
                        Pastikan aplikasi Nuxt 4 Anda mengirimkan header berikut pada setiap request:
                    </p>
                    <div class="bg-slate-900 border border-slate-800 text-slate-300 p-2.5 rounded-lg font-mono text-[10px] space-y-1">
                        <div>X-API-Key: {{ apiKey || 'BELUM_DI_SET_DI_CMS' }}</div>
                        <div class="text-slate-500">// atau Authorization: Bearer {{ apiKey || 'BELUM_DI_SET_DI_CMS' }}</div>
                    </div>
                    <p class="text-[10px] text-amber-600/90 italic">
                        * Token di atas terisi secara dinamis sesuai konfigurasi Anda di halaman Pengaturan Global.
                    </p>
                </div>

                <!-- Search feedback -->
                <div v-if="filteredEndpoints.length === 0" class="flex flex-col items-center justify-center p-12 text-center border border-dashed border-border/80 rounded-2xl bg-muted/10">
                    <AlertCircle class="h-8 w-8 text-muted-foreground/60 mb-2" />
                    <p class="text-sm font-semibold text-foreground">Tidak ada API ditemukan</p>
                    <p class="text-xs text-muted-foreground mt-1">Coba sesuaikan kata kunci pencarian atau ganti filter kategori.</p>
                </div>

                <div v-else class="space-y-8">
                    <!-- Endpoint Quick Selector -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pb-4 border-b border-border/40">
                        <button 
                            v-for="ep in filteredEndpoints" 
                            :key="ep.id"
                            @click="selectEndpoint(ep.id)"
                            class="p-3 text-left border rounded-xl transition-all duration-200 flex items-start gap-3 cursor-pointer group"
                            :class="selectedEndpointId === ep.id ? 'border-primary bg-primary/5 shadow-2xs' : 'border-border/70 bg-card/40 hover:border-border hover:bg-muted/20'"
                        >
                            <span 
                                class="px-2 py-0.5 rounded-md text-[9px] font-bold tracking-wider font-mono flex-shrink-0"
                                :class="{
                                    'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20': ep.method === 'GET',
                                    'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20': ep.method === 'POST',
                                }"
                            >
                                {{ ep.method }}
                            </span>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-foreground group-hover:text-primary transition-colors truncate">
                                    {{ ep.title }}
                                </p>
                                <p class="text-[10px] font-mono text-muted-foreground truncate mt-0.5">
                                    {{ ep.path }}
                                </p>
                            </div>
                        </button>
                    </div>

                    <!-- Selected Endpoint Detail Documentation -->
                    <div class="space-y-6 animate-fade-in-down">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span 
                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider font-mono"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20': selectedEndpoint.method === 'GET',
                                        'bg-indigo-500/10 text-indigo-600 border border-indigo-500/20': selectedEndpoint.method === 'POST',
                                    }"
                                >
                                    {{ selectedEndpoint.method }}
                                </span>
                                <span class="font-mono text-xs font-semibold text-foreground bg-muted px-3 py-1 rounded-md border border-border">
                                    {{ selectedEndpoint.path }}
                                </span>
                                <Badge variant="outline" class="text-[10px] px-2.5 bg-background">
                                    {{ selectedEndpoint.category }}
                                </Badge>
                                <span 
                                    class="text-[10px] flex items-center gap-1 font-medium px-2 py-0.5 rounded-full"
                                    :class="selectedEndpoint.isProtected ? 'bg-amber-500/10 text-amber-600' : 'bg-green-500/10 text-green-600'"
                                >
                                    <component :is="selectedEndpoint.isProtected ? Lock : Unlock" class="h-3 w-3" />
                                    {{ selectedEndpoint.isProtected ? 'Private (Auth required)' : 'Public API' }}
                                </span>
                            </div>
                            
                            <h2 class="text-lg font-bold text-foreground mt-3">{{ selectedEndpoint.title }}</h2>
                            <p class="text-xs text-muted-foreground mt-1.5 leading-relaxed bg-muted/30 p-3 rounded-lg border border-border/40">
                                {{ selectedEndpoint.description }}
                            </p>
                        </div>

                        <!-- Protection / Rate Limiting Alert -->
                        <div v-if="selectedEndpoint.rateLimit" class="bg-indigo-500/5 border border-indigo-500/10 p-3.5 rounded-xl flex items-start gap-2.5 text-xs text-indigo-600">
                            <Info class="h-4 w-4 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-bold">Rate Limiting:</span> {{ selectedEndpoint.rateLimit }}
                            </div>
                        </div>

                        <!-- Path Parameters Section -->
                        <div v-if="selectedEndpoint.pathParams && selectedEndpoint.pathParams.length > 0">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-foreground mb-3 flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary" /> Path Parameters
                            </h3>
                            <div class="border border-border/50 rounded-xl overflow-hidden bg-card/30">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-muted/50 border-b border-border/50 text-[10px] font-bold text-muted-foreground">
                                            <th class="p-3">Parameter</th>
                                            <th class="p-3">Type</th>
                                            <th class="p-3">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border/30">
                                        <tr v-for="param in selectedEndpoint.pathParams" :key="param.name" class="hover:bg-muted/10">
                                            <td class="p-3 font-mono font-bold text-primary">{{ param.name }}</td>
                                            <td class="p-3 font-mono text-[10px] text-muted-foreground">{{ param.type }}</td>
                                            <td class="p-3 text-muted-foreground">{{ param.description }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Query Parameters Section -->
                        <div v-if="selectedEndpoint.queryParams && selectedEndpoint.queryParams.length > 0">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-foreground mb-3 flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary" /> Query Parameters
                            </h3>
                            <div class="border border-border/50 rounded-xl overflow-hidden bg-card/30">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-muted/50 border-b border-border/50 text-[10px] font-bold text-muted-foreground">
                                            <th class="p-3">Parameter</th>
                                            <th class="p-3">Type</th>
                                            <th class="p-3 text-center">Required</th>
                                            <th class="p-3">Default</th>
                                            <th class="p-3">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border/30">
                                        <tr v-for="param in selectedEndpoint.queryParams" :key="param.name" class="hover:bg-muted/10">
                                            <td class="p-3 font-mono font-bold text-primary">{{ param.name }}</td>
                                            <td class="p-3 font-mono text-[10px] text-muted-foreground">{{ param.type }}</td>
                                            <td class="p-3 text-center">
                                                <span class="inline-block text-[9px] px-1.5 py-0.5 rounded font-bold" 
                                                      :class="param.required ? 'bg-rose-500/10 text-rose-500' : 'bg-muted text-muted-foreground'">
                                                    {{ param.required ? 'YES' : 'NO' }}
                                                </span>
                                            </td>
                                            <td class="p-3 font-mono text-muted-foreground">{{ param.default || '-' }}</td>
                                            <td class="p-3 text-muted-foreground leading-relaxed">{{ param.description }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Request Payload Fields Section -->
                        <div v-if="selectedEndpoint.requestFields && selectedEndpoint.requestFields.length > 0">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-foreground mb-3 flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary" /> Request Body Fields (JSON)
                            </h3>
                            <div class="border border-border/50 rounded-xl overflow-hidden bg-card/30">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-muted/50 border-b border-border/50 text-[10px] font-bold text-muted-foreground">
                                            <th class="p-3">Field</th>
                                            <th class="p-3">Type</th>
                                            <th class="p-3 text-center">Required</th>
                                            <th class="p-3">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border/30">
                                        <tr v-for="field in selectedEndpoint.requestFields" :key="field.name" class="hover:bg-muted/10">
                                            <td class="p-3 font-mono font-bold text-primary flex items-center gap-1">
                                                {{ field.name }}
                                                <span v-if="field.name === 'website'" class="text-[9px] bg-amber-500/10 text-amber-600 font-bold px-1 rounded hover:opacity-80 cursor-help" title="Anti-spam Honeypot Field">honeypot</span>
                                            </td>
                                            <td class="p-3 font-mono text-[10px] text-muted-foreground">{{ field.type }}</td>
                                            <td class="p-3 text-center">
                                                <span class="inline-block text-[9px] px-1.5 py-0.5 rounded font-bold" 
                                                      :class="field.required ? 'bg-rose-500/10 text-rose-500' : 'bg-muted text-muted-foreground'">
                                                    {{ field.required ? 'YES' : 'NO' }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-muted-foreground leading-relaxed">{{ field.description }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Interactive 'Try It Out' Dashboard -->
                        <div class="border border-border rounded-2xl overflow-hidden bg-card/25 shadow-sm">
                            <div class="bg-muted/50 px-5 py-3 border-b border-border flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Play class="h-4 w-4 text-emerald-500" />
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">Interactive Try It Out</h3>
                                </div>
                                <span class="text-[10px] text-muted-foreground">Menghubungi endpoint live di server lokal ini</span>
                            </div>
                            
                            <div class="p-5 space-y-4">
                                <!-- Try It Out Parameter Inputs -->
                                <div v-if="(selectedEndpoint.pathParams && selectedEndpoint.pathParams.length > 0) || (selectedEndpoint.queryParams && selectedEndpoint.queryParams.length > 0)" class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b border-border/30 pb-4">
                                    <!-- Path params inputs -->
                                    <div v-for="p in selectedEndpoint.pathParams" :key="p.name" class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-foreground uppercase tracking-wider flex items-center gap-1">
                                            {{ p.name }} <span class="text-rose-500 font-bold">*</span>
                                            <span class="text-[9px] text-muted-foreground lowercase">({{ p.type }} - path)</span>
                                        </label>
                                        <input 
                                            v-model="tryItOutParams[p.name]" 
                                            type="text" 
                                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-background focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    
                                    <!-- Query params inputs -->
                                    <div v-for="q in selectedEndpoint.queryParams" :key="q.name" class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-foreground uppercase tracking-wider flex items-center gap-1">
                                            {{ q.name }} 
                                            <span v-if="q.required" class="text-rose-500 font-bold">*</span>
                                            <span class="text-[9px] text-muted-foreground lowercase">({{ q.type }} - query)</span>
                                        </label>
                                        <input 
                                            v-model="tryItOutParams[q.name]" 
                                            type="text" 
                                            class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-background focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"
                                            :placeholder="q.default || ''"
                                        />
                                    </div>
                                </div>

                                <!-- Try It Out Request Body Inputs -->
                                <div v-if="selectedEndpoint.requestFields && selectedEndpoint.requestFields.length > 0" class="space-y-3.5 border-b border-border/30 pb-4">
                                    <h4 class="text-[10px] font-bold text-foreground uppercase tracking-wider">Payload Body Data</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="f in selectedEndpoint.requestFields" :key="f.name" class="space-y-1.5">
                                            <label class="text-[10px] font-bold text-foreground uppercase tracking-wider flex items-center gap-1">
                                                {{ f.name }}
                                                <span v-if="f.required" class="text-rose-500 font-bold">*</span>
                                                <span class="text-[9px] text-muted-foreground lowercase">({{ f.type }})</span>
                                            </label>
                                            <input 
                                                v-model="tryItOutBody[f.name]" 
                                                type="text" 
                                                class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-background focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"
                                                :placeholder="f.name === 'website' ? 'Tinggalkan kosong (honeypot)' : ''"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-muted-foreground flex items-center gap-1">
                                        <Info class="h-3.5 w-3.5 text-primary" />
                                        Metode: <b class="font-bold text-foreground">{{ selectedEndpoint.method }}</b> | URL: <code class="bg-muted px-1.5 py-0.5 rounded font-mono text-[9px] text-foreground">{{ selectedEndpoint.path }}</code>
                                    </span>
                                    <Button 
                                        size="sm" 
                                        @click="executeTryItOut" 
                                        :disabled="tryItOutLoading"
                                        class="cursor-pointer gap-2"
                                    >
                                        <RefreshCw v-if="tryItOutLoading" class="h-3 w-3 animate-spin" />
                                        <Play v-else class="h-3 w-3" />
                                        {{ tryItOutLoading ? 'Mengirim...' : 'Kirim Request' }}
                                    </Button>
                                </div>

                                <!-- Response Console Output -->
                                <div v-if="tryItOutResponse" class="space-y-2 mt-4 animate-fade-in">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-semibold text-foreground">Response Output:</span>
                                        <span 
                                            class="px-2 py-0.5 rounded font-mono text-[10px] font-bold uppercase"
                                            :class="{
                                                'bg-emerald-500/10 text-emerald-600': tryItOutResponse.status >= 200 && tryItOutResponse.status < 300,
                                                'bg-rose-500/10 text-rose-600': tryItOutResponse.status >= 400
                                            }"
                                        >
                                            HTTP {{ tryItOutResponse.status }} {{ tryItOutResponse.statusText }}
                                        </span>
                                    </div>
                                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 overflow-x-auto max-h-[300px]">
                                        <pre class="text-[11px] font-mono text-slate-300 leading-relaxed">{{ JSON.stringify(tryItOutResponse.body, null, 2) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Code Snippet & Static Responses -->
            <div class="w-full lg:w-2/5 bg-slate-950 text-slate-200 overflow-y-auto p-6 flex flex-col justify-between">
                <div class="space-y-6">
                    <!-- Heading -->
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                        <div class="flex items-center gap-2">
                            <Code class="h-4 w-4 text-primary" />
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Code Snippets & Mockup</h3>
                        </div>
                        <div class="flex border border-slate-800 rounded-lg overflow-hidden p-0.5 bg-slate-900">
                            <button 
                                v-for="lang in ['curl', 'js', 'nuxt']" 
                                :key="lang"
                                @click="activeCodeTab = lang as any"
                                class="px-2 py-1 text-[9px] font-bold rounded-md transition-all cursor-pointer uppercase"
                                :class="activeCodeTab === lang ? 'bg-primary text-primary-foreground' : 'text-slate-400 hover:text-slate-200'"
                            >
                                {{ lang === 'nuxt' ? 'Nuxt 4' : lang }}
                            </button>
                        </div>
                    </div>

                    <!-- Code Snippet Display -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 uppercase tracking-wider">Example Request:</span>
                            <button 
                                @click="copyToClipboard(generateCodeSnippet(selectedEndpoint, activeCodeTab))"
                                class="text-[10px] text-slate-400 hover:text-white transition flex items-center gap-1 bg-slate-900 hover:bg-slate-800 px-2 py-1 rounded-md border border-slate-800 cursor-pointer"
                            >
                                <component :is="copied ? Check : Copy" class="h-3.5 w-3.5" :class="copied ? 'text-green-400' : ''" />
                                {{ copied ? 'Copied!' : 'Copy Code' }}
                            </button>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 overflow-x-auto relative group">
                            <pre class="text-[11px] font-mono text-emerald-400 leading-relaxed whitespace-pre-wrap">{{ generateCodeSnippet(selectedEndpoint, activeCodeTab) }}</pre>
                        </div>
                    </div>

                    <!-- Static Sample Response Mock -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 uppercase tracking-wider">Sample Response (200 OK):</span>
                            <button 
                                @click="copyToClipboard(JSON.stringify(selectedEndpoint.successResponse, null, 2))"
                                class="text-[10px] text-slate-400 hover:text-white transition flex items-center gap-1 bg-slate-900 hover:bg-slate-800 px-2 py-1 rounded-md border border-slate-800 cursor-pointer"
                            >
                                <Copy class="h-3.5 w-3.5" />
                                Copy JSON
                            </button>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 overflow-x-auto max-h-[350px]">
                            <pre class="text-[11px] font-mono text-slate-300 leading-relaxed">{{ JSON.stringify(selectedEndpoint.successResponse, null, 2) }}</pre>
                        </div>
                    </div>
                </div>

                <!-- Technical Footer Metadata -->
                <div class="mt-8 border-t border-slate-850 pt-4 text-[10px] text-slate-500 font-mono space-y-1 bg-slate-900/40 p-3 rounded-lg border border-slate-800/60">
                    <div class="flex justify-between">
                        <span>Base URL:</span>
                        <span class="text-slate-400 font-bold">http://localhost:8000</span>
                    </div>
                    <div class="flex justify-between">
                        <span>API Engine:</span>
                        <span class="text-slate-400">Laravel v13.x</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Format:</span>
                        <span class="text-slate-400">JSON API (Accept: application/json)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-down {
    animation: fadeInDown 0.25s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in {
    animation: fadeIn 0.2s ease-out forwards;
}
</style>
