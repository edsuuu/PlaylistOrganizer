<div x-data="{ open: false }" x-on:toggle-sidebar.window="open = !open">
    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed inset-0 z-30 bg-black/50 lg:hidden"
    ></div>

    <aside
        x-cloak
        x-show="open"
        x-transition:enter="transition transform duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 start-0 z-40 w-64 bg-blue-black shadow-lg transform lg:translate-x-0 lg:static lg:hidden"
    >
        @include('layout.navbar-links')
    </aside>
</div>
