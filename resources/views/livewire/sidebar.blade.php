<div class="space-y-1">
    @foreach($playlists as $playlist)
        <a href="{{ route('edit-playlist', ['id' =>  $playlist['id']]) }}" wire:navigate  class="flex items-center gap-3 px-2 py-1 rounded-md bg-gray-700 cursor-pointer" wire:key="{{ $playlist['id'] }}">
            <div class="flex-shrink-0 w-10 h-10 bg-green-spotify rounded flex items-center justify-center">
                @if(isset($playlist['images'][0]['url']))
                    <img src="{{ $playlist['images'][0]['url'] }}" alt="{{ $playlist['id'] }}" class="object-cover">
                @else
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 11-1.414-1.414A7.971 7.971 0 0017 12c0-1.594-.471-3.076-1.283-4.243a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 12a5.983 5.983 0 01-.757 2.829 1 1 0 11-1.415-1.414A3.987 3.987 0 0013 12a3.987 3.987 0 00-.172-1.415 1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </div>
            <div class="flex flex-col">
                <small class="text-white font-medium w-full">{{ $playlist['name'] }}</small>
                <small class="text-gray-400">{{ $playlist['tracks']['total'] }} músicas</small>
            </div>
        </a>
    @endforeach
</div>

