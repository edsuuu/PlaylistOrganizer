<div>
    <h2>
        {{ __('Informações de Perfil') }}
    </h2>

    <form action="POST" wire:submit.prevent="saveProfile">
        @csrf
        <div class="max-w-xl flex flex-col gap-4">
            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[14px] font-medium">Nome completo</p>
                <input type="text" name="name" placeholder="Nome"
                       wire:model="name"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('name') border-red-500 @enderror"
                       maxlength="20">
                @error('firstName')
                <span class="text-red-500 text-[13px]">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[14px] font-medium">Email</p>
                <input type="email" name="name" placeholder="Sobrenome"
                       wire:model="email" disabled
                       class="border border-gray-300 bg-gray-200 outline-none p-2 pl-3 rounded focus:border-blue-link">
                @error('lastName')
                <span class="text-red-500 text-[13px]">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-black flex flex-col gap-0.5">
                <p class="text-gray-700 text-[14px] font-medium">Celular</p>
                <input type="text" name="cellphone"  placeholder="(99) 99999-9999"
                       wire:model="cellphone"
                       class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('cellphone') border-red-500 @enderror"
                       maxlength="15" x-mask:dynamic="maskPhone">
                @error('cellphone')
                <span class="text-red-500 text-[13px]">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-4 flex flex-row gap-5 items-center">
            <button type="submit" class="bg-blue-black text-white px-4 py-1 rounded-lg active:scale-[0.99]]">Salvar</button>
            <div x-data="{ shown: false, timeout: null }"
                 x-init="@this.on('sucessUpdatedProfile', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
                 x-show.transition.out.opacity.duration.1500ms="shown"
                 x-transition:leave.opacity.duration.1500ms
                 style="display: none;" class="text-sm text-green-600">
                <h1>Salvo com sucesso</h1>
            </div>
        </div>
    </form>
</div>
