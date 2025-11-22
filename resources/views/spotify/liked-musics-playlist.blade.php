<x-app-layout>
    <flux:main>
        <livewire:playlists.new-musics :id="request()->route('id')" :favorites-music="true"/>
    </flux:main>
</x-app-layout>
