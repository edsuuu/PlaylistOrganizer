<div class="w-[600px]">
    <div>
        <h1 class="text-2xl font-semibold">Alterar sua senha</h1>
    </div>

    <form method="POST" wire:submit.prevent="resetPassword" class="flex flex-col gap-2 mt-4">
        @csrf
        <div class="flex flex-col gap-10">
            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[15px] font-medium">Seu e-mail</p>
                <h1 class="border border-gray-300 bg-gray-200 outline-none p-2 pl-3 rounded focus:border-blue-link invalid:border-red-500">{{ $email }}</h1>
                @error('email')
                <span class="text-red-500 text-[13px] mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="flex flex-col gap-10">
            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[15px] font-medium">Nova senha</p>
                <input type="password" name="password" placeholder="Sua nova senha" wire:model="password"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link @error('password') border-red-500 @enderror ">
                @error('password')
                <span class="text-red-500 text-[13px] mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="flex flex-col gap-10">
            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[15px] font-medium">Confirmação de nova senha</p>
                <input type="password" name="password_confirmation" placeholder="Confirme sua nova senha" wire:model="password_confirmation"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link @error('password_confirmation') border-red-500 @enderror ">
                @error('password_confirmation')
                <span class="text-red-500 text-[13px] mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button type="submit" class="bg-blue-button w-full font-bold text-white-color rounded-[5px] py-2 transition-all duration-200 active:scale-[0.99] mt-4">
            Salvar
        </button>
    </form>
</div>
