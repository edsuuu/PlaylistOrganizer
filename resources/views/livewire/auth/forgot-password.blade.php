<div class="w-[600px]">
    <div>
        <h1 class="text-2xl font-semibold">Esqueceu sua senha ?</h1>
        <p>Encaminharemos um E-mail para recuperar sua senha.</p>
    </div>

    <form method="POST" wire:submit.prevent="send" class="flex flex-col gap-2 mt-4">
        @csrf
        <div class="flex flex-col gap-10">
            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[15px] font-medium">Seu e-mail</p>
                <input type="email" name="email" placeholder="Digite o seu email" wire:model="email"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link invalid:border-red-500">
                @error('email')
                <span class="text-red-500 text-[13px] mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
      <div class="relative">
          <button type="submit" class="bg-blue-button w-full font-bold text-white-color rounded-[5px] py-2 transition-all duration-200 active:scale-[0.99]">
              Enviar link para recuperar sua senha
          </button>

          <div wire:loading wire:target="send" class="absolute top-0 left-0 w-full h-full z-10">
              <div class="w-full h-full rounded-md flex items-center justify-center bg-white/70">
                  <x-heroicon-s-arrow-path class="text-[#20392f] w-5 h-5 animate-spin"/>
              </div>
          </div>
      </div>
    </form>
</div>
