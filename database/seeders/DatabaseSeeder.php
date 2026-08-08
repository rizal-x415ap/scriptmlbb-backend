<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create/Update Admin User
        $admin = User::updateOrCreate(
            ['email' => 'rizal@scriptmlbb.com'],
            [
                'name' => 'Rizal Efendi',
                'password' => Hash::make('#rizal15'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Categories
        $categories = [
            ['name' => 'Architecture', 'slug' => 'architecture', 'color_code' => '#006c45', 'description' => 'System architecture, edge networks, and distributed infrastructure.'],
            ['name' => 'Engineering', 'slug' => 'engineering', 'color_code' => '#3ecf8e', 'description' => 'Frontend frameworks, build engines, and browser performance.'],
            ['name' => 'Design Systems', 'slug' => 'design-systems', 'color_code' => '#934a23', 'description' => 'Typography scales, white canvas aesthetics, and component primitives.'],
            ['name' => 'AI & Tooling', 'slug' => 'ai-tooling', 'color_code' => '#24b47e', 'description' => 'Agentic coding assistants, developer CLI tools, and automation.'],
            ['name' => 'Product', 'slug' => 'product', 'color_code' => '#ffa072', 'description' => 'User experience retention, micro-interactions, and product philosophy.'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Create Tags
        $tags = ['Vue.js', 'Vite', 'Architecture', 'TailwindCSS', 'TypeScript', 'Design Systems', 'Laravel'];
        foreach ($tags as $t) {
            Tag::updateOrCreate(['slug' => Str::slug($t)], ['name' => $t]);
        }

        $archCategory = Category::where('slug', 'architecture')->first();
        $engCategory = Category::where('slug', 'engineering')->first();
        $dsCategory = Category::where('slug', 'design-systems')->first();
        $aiCategory = Category::where('slug', 'ai-tooling')->first();
        $prodCategory = Category::where('slug', 'product')->first();

        // 4. Featured Hero Article (EXACT HTML content as originally designed)
        $featuredContent = <<<'HTML'
<section id="introduction" class="space-y-4">
  <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-[#171717]">
    1. The Edge Latency Paradox
  </h2>
  <p>
    Traditional monolithic client-server architectures rely on centralized data centers for evaluating state changes. However, as global application traffic scales, round-trip network latency between client devices and central hubs creates perceptible delays that degrade user experience.
  </p>
  <p>
    By moving computation and caching to the physical network edge—closer to the end user—we drastically reduce network hops. The challenge shifts from physical distance to synchronized distributed state resolution.
  </p>

  <blockquote class="border-l-2 border-[#3ecf8e] pl-6 py-2 italic text-[#171717] bg-[#fafafa] rounded-r-[6px] my-6">
    "Eliminating unnecessary network hops is the single highest-leverage optimization a modern web application can make."
  </blockquote>
</section>

<section id="state-caching" class="space-y-4">
  <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-[#171717]">
    2. Distributed State Primitives
  </h2>
  <p>
    To achieve consistent sub-10ms response times without encountering race conditions, we partition application state into two distinct domains: <span class="font-semibold text-[#171717]">Ephemeral UI Mutations</span> and <span class="font-semibold text-[#171717]">Authoritative Storage Records</span>.
  </p>
  <ul class="list-disc pl-6 space-y-2 text-[#707070] text-base">
    <li><strong class="text-[#171717]">Optimistic Local Mutators:</strong> Immediately reflect state updates in component memory before network response.</li>
    <li><strong class="text-[#171717]">Edge Invalidation Tags:</strong> Invalidate cache entries at edge worker nodes in under 5 milliseconds globally.</li>
    <li><strong class="text-[#171717]">CRDT Conflict Resolution:</strong> Merge asynchronous concurrent edits without requiring blocking locks.</li>
  </ul>
</section>

<section id="code-implementation" class="space-y-4">
  <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-[#171717]">
    3. Implementation Details
  </h2>
  <p>
    Below is a sample TypeScript implementation of an edge memory worker handler leveraging optimistic cache invalidation and memory locks:
  </p>

  <div class="rounded-[6px] bg-[#1c1c1c] text-[#fafafa] p-6 border border-white/10 overflow-x-auto space-y-3 font-mono text-sm">
    <div class="flex items-center justify-between text-xs text-[#b2b2b2] border-b border-white/10 pb-2">
      <span class="font-mono-eyebrow text-[#3ecf8e]">EDGE_CACHE_WORKER.TS</span>
      <span>READ_ONLY</span>
    </div>
    <pre><code><span class="text-[#3ecf8e]">import</span> { createEdgeWorker } <span class="text-[#3ecf8e]">from</span> <span class="text-[#4ade80]">'@supabaze/edge-core'</span>;

<span class="text-[#3ecf8e]">export default</span> createEdgeWorker({
  <span class="text-[#3ecf8e]">async</span> fetch(request, env, ctx) {
    <span class="text-[#707070]">// 1. Intercept request at nearest edge node</span>
    <span class="text-[#3ecf8e]">const</span> cacheKey = <span class="text-[#3ecf8e]">new</span> URL(request.url).pathname;
    <span class="text-[#3ecf8e]">const</span> cachedResponse = <span class="text-[#3ecf8e]">await</span> env.EDGE_CACHE.get(cacheKey);

    <span class="text-[#3ecf8e]">if</span> (cachedResponse) {
      <span class="text-[#3ecf8e]">return new</span> Response(cachedResponse, {
        headers: { <span class="text-[#4ade80]">"X-Cache-Status"</span>: <span class="text-[#4ade80]">"HIT"</span> }
      });
    }

    <span class="text-[#707070]">// 2. Execute optimistic fallback & register invalidation listener</span>
    <span class="text-[#3ecf8e]">const</span> originResponse = <span class="text-[#3ecf8e]">await</span> fetch(request);
    ctx.waitUntil(env.EDGE_CACHE.put(cacheKey, originResponse.clone()));

    <span class="text-[#3ecf8e]">return</span> originResponse;
  }
});</code></pre>
  </div>
</section>

<section id="benchmarks" class="space-y-4">
  <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-[#171717]">
    4. Production Benchmarks
  </h2>
  <p>
    Across 1.2 million synthetic and real-user requests, edge state caching demonstrated substantial reductions in latency metrics across all geographic regions:
  </p>

  <div class="stitch-card overflow-hidden">
    <table class="w-full text-left text-sm">
      <thead class="bg-[#fafafa] border-b border-[#dfdfdf] text-[#171717] font-semibold">
        <tr>
          <th class="p-3.5 font-mono-eyebrow">Region</th>
          <th class="p-3.5 font-mono-eyebrow">Origin Latency</th>
          <th class="p-3.5 font-mono-eyebrow">Edge Cache</th>
          <th class="p-3.5 font-mono-eyebrow text-right">Improvement</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#dfdfdf] text-[#707070]">
        <tr>
          <td class="p-3.5 font-medium text-[#171717]">US East (N. Virginia)</td>
          <td class="p-3.5 font-mono">112ms</td>
          <td class="p-3.5 font-mono text-[#006c45] font-semibold">6ms</td>
          <td class="p-3.5 text-right font-mono text-[#006c45] font-semibold">18.6x faster</td>
        </tr>
        <tr>
          <td class="p-3.5 font-medium text-[#171717]">EU Central (Frankfurt)</td>
          <td class="p-3.5 font-mono">148ms</td>
          <td class="p-3.5 font-mono text-[#006c45] font-semibold">8ms</td>
          <td class="p-3.5 text-right font-mono text-[#006c45] font-semibold">18.5x faster</td>
        </tr>
        <tr>
          <td class="p-3.5 font-medium text-[#171717]">AP Southeast (Singapore)</td>
          <td class="p-3.5 font-mono">210ms</td>
          <td class="p-3.5 font-mono text-[#006c45] font-semibold">9ms</td>
          <td class="p-3.5 text-right font-mono text-[#006c45] font-semibold">23.3x faster</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section id="conclusion" class="space-y-4 pt-4 border-t border-[#dfdfdf]">
  <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-[#171717]">
    5. Final Recommendations
  </h2>
  <p>
    Adopting edge state caching is no longer reserved for high-traffic enterprise infrastructures. With modern developer tooling like Vue 3 and Vite, edge invalidation patterns can be seamlessly integrated into standard build pipelines.
  </p>
</section>
HTML;

        $featured = Article::updateOrCreate(
            ['slug' => 'designing-zero-latency-edge-architecture'],
            [
                'category_id' => $archCategory->id,
                'author_id' => $admin->id,
                'title' => 'Designing Zero-Latency Edge Architecture with Distributed State Caching',
                'subtitle' => 'Building multi-region edge networks that achieve sub-10ms response times by leveraging distributed memory primitives and optimistic UI mutations.',
                'excerpt' => 'An in-depth exploration into building multi-region edge networks that achieve sub-10ms response times by leveraging distributed memory primitives.',
                'content' => $featuredContent,
                'cover_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '8 min read',
                'status' => 'published',
                'is_featured' => true,
                'likes_count' => 342,
                'views_count' => 1280,
                'published_at' => now()->subDay(),
            ]
        );

        // 5. Article 2
        Article::updateOrCreate(
            ['slug' => 'tailwind-css-v4-rethinking-utility-engine-performance'],
            [
                'category_id' => $engCategory->id,
                'author_id' => $admin->id,
                'title' => 'Tailwind CSS v4: Rethinking Utility Engine Performance and Zero-Config Build Trees',
                'subtitle' => 'How the new Rust-powered Lightning CSS parser accelerates full-project build times by up to 10x while removing legacy PostCSS overhead.',
                'excerpt' => 'How the new Rust-powered Lightning CSS parser accelerates full-project build times by up to 10x while removing legacy PostCSS overhead.',
                'content' => "<p>Tailwind CSS v4 introduces a brand-new engine written from scratch in Rust. By unifying CSS parsing, auto-prefixing, and custom utility generation under Lightning CSS, full-project build trees compile in milliseconds.</p><h3 class=\"text-xl font-semibold mt-4 mb-2\">Key Engine Improvements</h3><ul class=\"list-disc pl-6 space-y-2\"><li><strong>Zero-Config Cascading Layering:</strong> Direct <code>@import \"tailwindcss\";</code> without requiring config boilerplate.</li><li><strong>Lightning CSS Parser:</strong> Parses CSS AST up to 10x faster than legacy JavaScript plugins.</li></ul>",
                'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '5 min read',
                'status' => 'published',
                'is_featured' => false,
                'likes_count' => 189,
                'views_count' => 840,
                'published_at' => now()->subDays(2),
            ]
        );

        // 6. Article 3
        Article::updateOrCreate(
            ['slug' => 'the-anti-slop-design-movement'],
            [
                'category_id' => $dsCategory->id,
                'author_id' => $admin->id,
                'title' => 'The Anti-Slop Design Movement: Elevating Developer Interfaces Beyond Generic Libraries',
                'subtitle' => 'Why strict typography rules, custom geometric scales, and high-contrast monochrome canvas design are replacing bloated UI kits.',
                'excerpt' => 'Why strict typography rules, custom geometric scales, and high-contrast monochrome canvas design are replacing bloated UI kits.',
                'content' => "<p>Clean editorial design relies on intentional whitespace, strict typography, and functional color usage.</p><blockquote class=\"border-l-2 border-[#3ecf8e] pl-6 py-2 italic text-[#171717] bg-[#fafafa] rounded-r-[6px] my-6\">\"High-contrast monochrome canvas balance eliminates visual distraction.\"</blockquote>",
                'cover_image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '6 min read',
                'status' => 'published',
                'is_featured' => false,
                'likes_count' => 245,
                'views_count' => 960,
                'published_at' => now()->subDays(4),
            ]
        );

        // 7. Article 4
        Article::updateOrCreate(
            ['slug' => 'building-resilient-vue-3-applications'],
            [
                'category_id' => $engCategory->id,
                'author_id' => $admin->id,
                'title' => 'Building Resilient Vue 3 Applications with Async Component Boundaries & Suspense',
                'subtitle' => 'Practical patterns for graceful fallback rendering, error boundaries, and optimistic data mutations in large enterprise Vue codebases.',
                'excerpt' => 'Practical patterns for graceful fallback rendering, error boundaries, and optimistic data mutations in large enterprise Vue codebases.',
                'content' => "<p>Vue 3 Composition API with <code>&lt;script setup&gt;</code> offers reactive primitives for managing complex application states.</p><p>Combining <code>defineAsyncComponent</code> with skeleton loaders ensures that users receive instant visual feedback.</p>",
                'cover_image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '7 min read',
                'status' => 'published',
                'is_featured' => false,
                'likes_count' => 128,
                'views_count' => 520,
                'published_at' => now()->subDays(6),
            ]
        );

        // 8. Article 5
        Article::updateOrCreate(
            ['slug' => 'ai-code-assistants-in-production'],
            [
                'category_id' => $aiCategory->id,
                'author_id' => $admin->id,
                'title' => 'AI Code Assistants in Production: Balancing Autonomy with Architectural Guardrails',
                'subtitle' => 'A retrospective on integrating agentic workflows directly into dev toolchains without sacrificing code quality or security compliance.',
                'excerpt' => 'A retrospective on integrating agentic workflows directly into dev toolchains without sacrificing code quality or security compliance.',
                'content' => "<p>Deploying AI coding assistants in enterprise environments requires setting strict architectural guardrails, automated linting checks, and automated unit testing verification before pull requests are merged.</p>",
                'cover_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '9 min read',
                'status' => 'published',
                'is_featured' => false,
                'likes_count' => 412,
                'views_count' => 1540,
                'published_at' => now()->subDays(8),
            ]
        );

        // 9. Article 6
        Article::updateOrCreate(
            ['slug' => 'white-canvas-philosophy-eliminating-ui-bloat'],
            [
                'category_id' => $prodCategory->id,
                'author_id' => $admin->id,
                'title' => 'White Canvas Philosophy: Eliminating UI Bloat in Modern Engineering Blogs',
                'subtitle' => 'Discover how zero-shadow layers, hairline outlines, and single-accent green highlights improve reader retention and visual density.',
                'excerpt' => 'Discover how zero-shadow layers, hairline outlines, and single-accent green highlights improve reader retention and visual density.',
                'content' => "<p>High-density editorial design focuses on content readability above all else. By paring down visual elements to crisp typography, subtle hairline borders, and responsive grid layouts, technical blogs achieve clean, distracting-free reading experiences.</p>",
                'cover_image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80',
                'read_time' => '4 min read',
                'status' => 'published',
                'is_featured' => false,
                'likes_count' => 298,
                'views_count' => 1100,
                'published_at' => now()->subDays(10),
            ]
        );

        // 10. Sample Comments
        Comment::updateOrCreate(
            ['content' => 'The section on optimistic state mutations at edge nodes is fantastic.'],
            [
                'article_id' => $featured->id,
                'author_name' => 'Sarah Chen',
                'author_email' => 'sarah@example.com',
                'author_avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
                'status' => 'approved',
            ]
        );

        Comment::updateOrCreate(
            ['content' => 'Tailwind v4 Lightning CSS parser completely fixed our monorepo build speed!'],
            [
                'article_id' => $featured->id,
                'author_name' => 'Marcus Vance',
                'author_email' => 'marcus@example.com',
                'author_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
                'status' => 'approved',
            ]
        );

        // 11. Sample Subscriber
        NewsletterSubscriber::updateOrCreate(
            ['email' => 'dev@supabaze.com'],
            ['status' => 'active', 'subscribed_at' => now()]
        );

        // 12. Default Site Settings (Blogger-Style)
        $defaultSettings = [
            'siteTitle' => 'SUPABAZE // Editorial & Tech',
            'siteDescription' => 'High-density technical blog and engineering design system.',
            'metaKeywords' => 'Vue3, Vite, Laravel, Architecture, TailwindCSS',
            'faviconUrl' => 'https://vitejs.dev/logo.svg',
            'brandLogoText' => 'SUPABAZE',
            'showAnnouncementBar' => 'true',
            'announcementText' => '⚡ TAILWIND V4 & VUE 3 ARCHITECTURE DISPATCH IS NOW LIVE',
            'announcementLink' => '/article/tailwind-css-v4-rethinking-utility-engine-performance',
            'showMostReadWidget' => 'true',
            'showAuthorWidget' => 'true',
            'showNewsletterWidget' => 'true',
            'showTopicsWidget' => 'true',
            'footerCopyright' => '© 2026 SUPABAZE INC. ALL RIGHTS RESERVED.',
            'footerBio' => 'Writing about high-throughput web systems, front-end performance tooling, and anti-slop user experience designs.',
            'twitterUrl' => 'https://twitter.com',
            'githubUrl' => 'https://github.com',
            'linkedinUrl' => 'https://linkedin.com',
        ];

        foreach ($defaultSettings as $k => $v) {
            \App\Models\SiteSetting::updateOrCreate(
                ['key' => $k],
                ['value' => (string) $v, 'group' => 'general']
            );
        }

        // 13. Default Static Pages (Tentang Kami, Privacy Policy, Terms)
        $defaultPages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'meta_description' => 'Informasi tentang portal penyedia script skin Mobile Legends terupdate dan terpercaya.',
                'content' => '<h2>Tentang Portal Script MLBB</h2><p>Selamat datang di portal resmi informasi dan penyedia script skin Mobile Legends Bang Bang. Kami berkomitmen untuk menyajikan update patch script skin terbaru, tutorial pemasangan tanpa root, serta link download aman dan teruji di Season 35.</p><h3>Visi & Misi Kami</h3><p>Memberikan akses informasi modifikasi visual script yang aman, no password, tanpa iklan mengganggu, serta 100% terverifikasi aman dari risiko banned.</p>',
                'status' => 'published',
            ],
            [
                'title' => 'Kebijakan Privasi (Privacy Policy)',
                'slug' => 'kebijakan-privasi',
                'meta_description' => 'Kebijakan privasi privasi pengunjung portal Script MLBB.',
                'content' => '<h2>Kebijakan Privasi</h2><p>Kami sangat menghargai privasi pengunjung portal kami. Dokumen Kebijakan Privasi ini menjelaskan jenis informasi pribadi yang diterima dan dikumpulkan oleh kami serta bagaimana informasi tersebut digunakan.</p><h3>Data Komentar & Ulasan</h3><p>Saat pengunjung meninggalkan ulasan atau komentar di situs, kami mengumpulkan data yang ditampilkan di form komentar, serta alamat IP pengunjung untuk membantu mendeteksi spam. Email Anda tidak akan pernah dipublikasikan.</p>',
                'status' => 'published',
            ],
            [
                'title' => 'Syarat & Ketentuan (Terms of Service)',
                'slug' => 'syarat-dan-ketentuan',
                'meta_description' => 'Syarat dan ketentuan penggunaan portal Script MLBB.',
                'content' => '<h2>Syarat & Ketentuan Penggunaan</h2><p>Dengan mengakses dan menggunakan portal ini, Anda menyetujui untuk terikat oleh syarat dan ketentuan penggunaan di bawah ini. Semua file script skin disajikan untuk tujuan edukasi dan kustomisasi pratinjau visual pribadi pengguna.</p>',
                'status' => 'published',
            ],
        ];

        foreach ($defaultPages as $pageData) {
            \App\Models\Page::updateOrCreate(['slug' => $pageData['slug']], $pageData);
        }
    }
}
