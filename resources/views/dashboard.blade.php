<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-8">

        <div>
            <h1 class="text-3xl font-bold">
                Personal Branding CMS
            </h1>

            <p class="mt-2 text-neutral-500">
                Welcome to your website management dashboard.
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-4">

            <div class="rounded-xl border p-6">
                <div class="text-sm text-neutral-500">
                    Website
                </div>

                <div class="mt-2 text-xl font-semibold">
                    Indra Paradana
                </div>
            </div>

            <div class="rounded-xl border p-6">
                <div class="text-sm text-neutral-500">
                    Database
                </div>

                <div class="mt-2 text-xl font-semibold">
                    MySQL
                </div>
            </div>

            <div class="rounded-xl border p-6">
                <div class="text-sm text-neutral-500">
                    Framework
                </div>

                <div class="mt-2 text-xl font-semibold">
                    Personal Branding
                </div>
            </div>

            <div class="rounded-xl border p-6">
                <div class="text-sm text-neutral-500">
                    Status
                </div>

                <div class="mt-2 text-xl font-semibold text-green-600">
                    Online
                </div>
            </div>

        </div>

        <div class="rounded-xl border p-6">

            <h2 class="text-xl font-semibold">
                CMS Modules
            </h2>

            <div class="mt-6 grid gap-4 md:grid-cols-4">

                <a href="{{ route('admin.site') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Site
                </a>

                <a href="{{ route('admin.medsos') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Social Media
                </a>

                <a href="{{ route('admin.hero') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Hero
                </a>

                <a href="{{ route('admin.about') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    About
                </a>

                <a href="{{ route('admin.resume') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Resume
                </a>

                <a href="{{ route('admin.portfolio') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Portfolio
                </a>

                <a href="{{ route('admin.services') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Services
                </a>

                <a href="{{ route('admin.testimonials') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Testimonials
                </a>

                <a href="{{ route('admin.faq') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    FAQ
                </a>

                <a href="{{ route('admin.contact') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Contact
                </a>

                <a href="{{ route('admin.footer') }}" class="rounded-lg border p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    Footer
                </a>

            </div>

        </div>

    </div>
</x-layouts::app>