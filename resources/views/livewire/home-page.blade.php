<div>
    <div class="h-screen bg-container-spotify flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/4 left-1/12 w-32 h-32 md:w-48 md:h-48 bg-green-spotify bg-opacity-10 rounded-full animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/12 w-24 h-24 md:w-36 md:h-36 bg-green-spotify bg-opacity-5 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/4 w-16 h-16 md:w-24 md:h-24 bg-green-spotify bg-opacity-5 rounded-full animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <div class="absolute inset-0 bg-gradient-radial from-green-spotify/10 via-transparent to-transparent"></div>

        <div class="relative z-10 text-center px-4 md:px-6 max-w-4xl mx-auto">
            <div class="mb-8 md:mb-12">
                <div class="inline-flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-green-spotify rounded-full flex items-center justify-center">
                        <span class="text-3xl md:text-4xl">🎵</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold text-white">
                        Playlist<span class="text-green-spotify">Organizer</span>
                    </h1>
                </div>
                <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed">
                    Transforme o caos musical em uma experiência organizada e personalizada
                </p>
            </div>

            <div class="mb-12 md:mb-16">
                <h2 class="text-2xl md:text-4xl font-bold text-white mb-4 bg-gradient-to-r from-white via-green-spotify to-white bg-clip-text text-transparent bg-300% animate-gradient">
                    A evolução do seu Spotify chegou
                </h2>
                <p class="text-base md:text-lg text-gray-400 max-w-xl mx-auto">
                    Organize, categorize e descubra suas músicas como nunca antes
                </p>
            </div>


            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-5">
                <a href="{{ route('spotify-auth') }}"
                   class="w-full sm:w-auto px-8 py-4 flex items-center gap-4 bg-green-spotify text-white text-xl font-semibold rounded-full transition-all duration-300 hover:bg-green-600 hover:scale-105 hover:shadow-lg hover:shadow-green-spotify/20 text-center">
                    <img src="{{ asset('images/spotify.svg') }}" alt="spotify" class="w-10 h-10">
                    Login com Spotify
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-500 inline-flex p-2 px-6 rounded-md">
                    <span class="text-white font-bold"> {{ session('error') }}</span>
                </div>
            @endif
        </div>
    </div>


    <style>
        .bg-gradient-radial {
            background: radial-gradient(circle at center, var(--tw-gradient-stops));
        }

        .animate-gradient {
            background-size: 300% 300%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        html {
            scroll-behavior: smooth;
        }
    </style>

    <script>
        document.querySelector('a[href="#features"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('features').scrollIntoView({
                behavior: 'smooth'
            });
        });

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.bg-container-spotify');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease-out';
                observer.observe(card);
            });
        });
    </script>
</div>
