<x-app-layout>
    <flux:main class="cst-scrollbar">
        <livewire:playlists.view-playlist :id="request()->route('id')"/>
    </flux:main>
</x-app-layout>
