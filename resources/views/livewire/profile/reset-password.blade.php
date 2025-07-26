<div>
    <h2>
        {{ __('Alterar senha') }}
    </h2>
    <form action="POST" wire:submit.prevent="saveNewPassword">
        @csrf
        <div class="max-w-xl flex flex-col gap-4">
            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[14px] font-medium">Senha Atual</p>
                <input type="password" placeholder="Senha Atual"
                       wire:model="currentPassword"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('currentPassword') border-red-500 @enderror">
                @error('currentPassword')
                <span class="text-red-500 text-[13px]">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[14px] font-medium">Nova senha</p>
                <input type="password" placeholder="Nova senha" wire:model="newPassword"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('newPassword') border-red-500 @enderror  @error('confirmNewPassword') border-red-500 @enderror">
                @error('newPassword')
                <span class="text-red-500 text-[13px]">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[14px] font-medium">Confirme sua nova senha</p>
                <input type="password" placeholder="Confirme sua nova senha"
                       wire:model="confirmNewPassword"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('confirmNewPassword') border-red-500 @enderror">
                @error('confirmNewPassword')
                <span class="text-red-500 text-[13px]">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-4 flex flex-row gap-5 items-center">
            <button type="submit" class="bg-blue-black text-white px-4 py-1 rounded-lg active:scale-[0.99]]">Salvar</button>
            <div x-data="{ shown: false, timeout: null }"
                 x-init="@this.on('successUpdatePassword', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
                 x-show.transition.out.opacity.duration.1500ms="shown"
                 x-transition:leave.opacity.duration.1500ms
                 style="display: none;" class="text-sm text-green-600">
                <h1>Salvo com sucesso</h1>
            </div>
        </div>

    </form>
</div>
