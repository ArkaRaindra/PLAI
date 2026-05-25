<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';

    const portals = [
        {
            title: 'Super Admin',
            description: 'Kelola sistem dan pengguna',
            href: '/super-admin/login',
        },
        {
            title: 'Auditor',
            description: 'Pelaksanaan audit dan penilaian',
            href: '/auditor/login',
        },
        {
            title: 'Fakultas',
            description: 'Koordinasi tingkat fakultas',
            href: '/fakultas/login',
        },
        {
            title: 'Prodi / Unit Penunjang',
            description: 'Program studi dan unit penunjang',
            href: '/prodi/login',
        },
    ];
</script>

<AppHead title="Portal Masuk">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</AppHead>

<div
    class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
>
    <main class="w-full max-w-4xl">
        <div class="mb-8 text-center">
            <img
                src="assets/images/logo/PLAI-BMD - HORIZONTAL.webp"
                alt="Logo SPMI PLAI"
                class="mx-auto mb-4 h-14 w-auto"
            />
            <h1 class="text-2xl font-semibold tracking-tight">SPMI</h1>
            <p class="mt-2 text-xl">
                Sistem Penjaminan Mutu Internal
            </p>
            <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                Pilih portal sesuai peran Anda untuk masuk ke sistem.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            {#each portals as portal (portal.href)}
                <a
                    href={portal.href}
                    class="group rounded-xl border border-[#e3e3e0] bg-white p-6 shadow-sm transition hover:border-[#f53003] hover:shadow-md dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:border-[#FF4433]"
                >
                    <h2
                        class="text-lg font-medium text-[#1b1b18] group-hover:text-[#f53003] dark:text-[#EDEDEC] dark:group-hover:text-[#FF4433]"
                    >
                        {portal.title}
                    </h2>
                    <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        {portal.description}
                    </p>
                    <span
                        class="mt-4 inline-flex items-center text-sm font-medium text-[#f53003] dark:text-[#FF4433]"
                    >
                        Masuk
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="ml-1 h-4 w-4 transition group-hover:translate-x-1"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </span>
                </a>
            {/each}
        </div>
    </main>
</div>
