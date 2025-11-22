<x-app-layout>
    <flux:main>
        <livewire:playlists.new-musics :id="request()->route('id')"/>
    </flux:main>
</x-app-layout>
