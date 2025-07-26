@if(isset($tenant->name))
    @section('title', \Illuminate\Support\Str::ucfirst(strtolower($tenant->name)))
@endif

<div class="bg-[#0f3460]">

    <div class="min-h-screen w-full">
        <div class="flex flex-col h-screen">
            @if(auth()->user())
                <div class="absolute flex flex-row gap-2 items-center p-2 top-4 right-4">
                    <a href="{{ route('dashboard') }}" class="text-white hover:underline">Dashboard</a>
                    <button
                        wire:click="openModalEdit"
                        class="bg-white rounded-full cursor-pointer p-2 top-4 right-4">
                        Editar
                    </button>
                </div>
            @endif
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-4 p-4">
                <div class="lg:col-span-3 flex flex-col items-center justify-center text-center">
                    <div
                        class="w-24 h-24 lg:w-32 lg:h-32 rounded-full overflow-hidden  shadow-md mb-3">
                        <img src="{{  url('image/' . $tenant->path) }}" alt="Logo"
                             class="w-full h-full object-cover">
                    </div>

                    <h1 class="text-2xl lg:text-4xl font-bold text-white text-glow mb-2 animate-pulse-slow">
                        {{ $this->tenant?->title ?? $this->tenant?->name }}
                    </h1>

                    <div class="text-white/30 text-xs mb-2">
                        @if($this->tenant?->since)
                            Desde {{ $this->tenant?->since ? \Carbon\Carbon::parse($this->tenant?->since)->year : '' }}
                        @endif
                    </div>

                    <p class="text-lg text-white/90 mb-4">
                        {{ $this->tenant?->description ?? '' }}
                    </p>

                    <div class="flex gap-4 mb-4">
                        @if($this->tenant?->whatsapp !== null)
                            <a href="#" onclick="openWhatsApp()"
                               class="w-10 h-10 glass rounded-full flex items-center justify-center text-white hover:scale-110 hover:bg-green-500/30 transition-all duration-300">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                        @endif

                        @if($this->tenant?->instagram !== null)
                            <a href="#" onclick="openInstagram()"
                               class="w-10 h-10 glass rounded-full flex items-center justify-center text-white hover:scale-110 hover:bg-pink-500/30 transition-all duration-300">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                        @endif
                        @if($this->tenant?->facebook !== null)
                            <a href="#" onclick="openFacebook()"
                               class="w-10 h-10 glass rounded-full flex items-center justify-center text-white hover:scale-110 hover:bg-blue-500/30 transition-all duration-300">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                        @endif
                        @if($this->tenant?->youtube !== null)
                            <a href="#" onclick="openYouTube()"
                               class="w-10 h-10 glass rounded-full flex items-center justify-center text-white hover:scale-110 hover:bg-red-500/30 transition-all duration-300">
                                <i class="fab fa-youtube text-lg"></i>
                            </a>
                        @endif
                        @if($this->tenant?->tiktok !== null)
                            <a href="#" onclick="openTikTok()"
                               class="w-10 h-10 glass rounded-full flex items-center justify-center text-white hover:scale-110 hover:bg-gray-800/30 transition-all duration-300">
                                <i class="fab fa-tiktok text-lg"></i>
                            </a>
                        @endif
                    </div>

                    <a href="{{ route('schedule') }}" wire:navigate
                        class="bg-blue-500 hover:bg-blue-600 rounded-md inline-flex p-2 px-5 cursor-pointer transition-colors mt-2">
                        <span class="text-white font-medium">Fazer um agendamento</span>
                    </a>
                </div>

                <div class="lg:col-span-2 glass rounded-2xl p-4 hover:bg-white/15 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="far fa-clock text-2xl text-blue-300"></i>
                        <h2 class="text-lg lg:text-xl font-bold text-white">Horário de Atendimento</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach ($orderedActiveDays as $day)
                            @if ($this->tenant?->hours[$day])
                                <div
                                    class="flex justify-between items-center bg-white/10 rounded-lg p-2 hover:bg-white/20 transition-all">
                                    <span class="text-white font-medium capitalize">{{ $day }}</span>
                                    <span class="text-white/80">{{ $this->tenant?->hours[$day]['start'] }} - {{ $this->tenant?->hours[$day]['end'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="glass rounded-2xl p-4 hover:bg-white/15 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-map-marker-alt text-2xl text-blue-300"></i>
                        <h2 class="text-lg lg:text-xl font-bold text-white">Localização</h2>
                    </div>

                    <div class="text-white/90 mb-4 text-sm">
                        <p>{{ $this->tenant?->address }}, {{ $this->tenant?->number }}</p>
                        <p>{{ $this->tenant?->neighborhood }} - {{ $this->tenant?->city }}, {{ $this->tenant?->uf }}</p>
                        <p>CEP: {{ $this->tenant?->zip_code }}</p>
                    </div>

                    <div
                        class="h-48 lg:h-64 w-full rounded-lg overflow-hidden border border-white/10 shadow-inner relative group cursor-pointer"
                        onclick="window.open('https://www.google.com/maps?q=' + @js($qString, JSON_THROW_ON_ERROR), '_blank')">
                        <iframe
                            title="Google Maps"
                            src="https://maps.google.com/maps?q={{$qString}}&output=embed"
                            class="w-full h-full pointer-events-none"
                            style="border:0;"
                            loading="lazy"
                            allowfullscreen="">
                        </iframe>
                        <div
                            class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-200"></div>
                    </div>
                </div>
            </div>

            <footer class="text-white/40 text-xs text-center py-2">
                Plataforma de agendamento online para pequeno empreendedor
            </footer>
        </div>
    </div>
</div>
