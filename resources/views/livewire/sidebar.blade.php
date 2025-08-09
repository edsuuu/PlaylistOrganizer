<div class="w-72 bg-gradient-to-b from-gray-800 to-sidebar-spotify flex flex-col border-r border-gray-800">
    <div class="p-6 border-b border-gray-800 flex items-center justify-between ">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-spotify rounded-full flex items-center justify-center">
                @if (Auth::user()->spotify->avatar)
                    <img src="{{ Auth::user()->spotify->avatar }}" class="rounded-full truncate" alt="avatar">
                @else
                    <span class="text-white font-bold text-sm">🎵</span>
                @endif
            </div>
            <span class="font-bold text-lg">{{ Auth::user()->name ?? 'PlaylistOrganizer' }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}"  class="flex items-center gap-3 p-2 cursor-pointer">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 cursor-pointer text-white">
                <span class="text-sm hover:underline">Sair</span>
            </button>
        </form>
    </div>

    <div class="flex-1 px-2 overflow-y-auto cst-scrollbar">
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition-colors group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                <span class="text-gray-300 group-hover:text-white font-medium">Dashboard</span>
            </a>

            {{--                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition-colors group">--}}
            {{--                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
            {{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>--}}
            {{--                    </svg>--}}
            {{--                    <span class="text-gray-300 group-hover:text-white font-medium">Buscar</span>--}}
            {{--                </a>--}}

            {{--                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 transition-colors group">--}}
            {{--                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
            {{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>--}}
            {{--                    </svg>--}}
            {{--                    <span class="text-gray-300 group-hover:text-white font-medium">Playlists</span>--}}
            {{--                </a>--}}
        </div>

        <div class="mt-4 pb-5">
            <div class="flex items-center justify-between mb-4 px-2">
                <h3 class="text-gray-400 font-semibold text-sm uppercase tracking-wider">Minhas Playlists</h3>
                {{--                    <button class="text-gray-400 hover:text-white transition-colors">--}}
                {{--                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
                {{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>--}}
                {{--                        </svg>--}}
                {{--                    </button>--}}
            </div>

            <div class="space-y-2">
                @foreach($playlists as $playlist)
                    <a href="{{ route('edit-playlist', ['id' =>  $playlist['id']]) }}" wire:navigate  class="flex items-center gap-3 px-2 py-2 rounded-md bg-gray-700 cursor-pointer" wire:key="{{ $playlist['id'] }}">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-spotify rounded flex items-center justify-center">
                            @if(isset($playlist['images'][0]['url']))
                                <img src="{{ $playlist['images'][0]['url'] }}" alt="{{ $playlist['id'] }}" class="object-cover">
                            @else
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 11-1.414-1.414A7.971 7.971 0 0017 12c0-1.594-.471-3.076-1.283-4.243a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 12a5.983 5.983 0 01-.757 2.829 1 1 0 11-1.415-1.414A3.987 3.987 0 0013 12a3.987 3.987 0 00-.172-1.415 1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm w-full">{{ $playlist['name'] }}</p>
                            <p class="text-gray-400 text-sm">{{ $playlist['tracks']['total'] }} músicas</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
