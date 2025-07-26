<div class="border border-black h-screen w-full flex flex-col justify-center items-center p-4">
    <div class="flex flex-col justify-center mt-3 mb-3">
        <h1 class="text-2xl font-bold text-black">
            Crie sua conta
        </h1>
        <div>
            @if ($errors->first('google'))
                <span class="text-red-500 text-[15px] font-medium">{{ $errors->first('google') }}</span>
            @endif

            @if ($errors->first('error'))
                <div class="alert alert-danger">
                    <span class="text-red-500 text-[15px] font-medium">{{ $errors->first('error') }}</span>
                </div>
            @endif
        </div>
    </div>
    <div class="w-[800px]">
        <form method="POST" wire:submit.prevent="save" class="flex flex-col gap-2">
            @csrf
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="name"
                        label="Nome"
                        type="text"
                        required="true"
                        placeholder="Nome completo do responsável"
                        error-message="{{ $errors->first('name') }}"
                        wire:model="name"
                    />

                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="email"
                        label="Digite o seu email"
                        type="email"
                        required="true"
                        placeholder="Nome completo do responsável"
                        error-message="{{ $errors->first('email') }}"
                        wire:model="email"
                    />
                </div>


                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="nameBusiness"
                        label="Nome do seu comercio"
                        type="text"
                        required="true"
                        placeholder="Nome do seu comercio"
                        error-message="{{ $errors->first('nameBusiness') }}"
                        wire:model="nameBusiness"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <label class="text-gray-700 text-[14px] font-medium block mb-1">
                        Nome do domínio para o seu comércio *
                    </label>
                    <div @class([
                                'flex flex-row items-stretch border focus-within:border-blue-link rounded overflow-hidden',
                                'border-gray-300' => !$errors->first('domain'),
                                'border-red-500' => $errors->first('domain')
                            ])>
                    <input
                            type="text"
                            name="domain"
                            placeholder="ex: nomedaloja"
                            class="border-none flex-1 p-2 pl-3 outline-none focus:ring-0"
                            wire:model="domain"
                            oninput="this.value = this.value.replace(/[^a-z0-9-]/g, '')">
                        <div class="flex items-center px-3 bg-gray-100 text-gray-700 text-sm whitespace-nowrap select-none">
                            .{{ config('app.base_domain', 'localhost') }}
                        </div>
                    </div>
                    @error('domain')
                        <span class="text-red-500 text-[13px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="documents"
                        label="CPF/CNPJ"
                        type="text"
                        required="true"
                        placeholder="CPF/CNPJ"
                        error-message="{{ $errors->first('documents') }}"
                        wire:model.live="documents"
                        oninput="this.value = documentMask(this.value)"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="cellphone"
                        label="Celular"
                        type="text"
                        required="true"
                        placeholder="Celular"
                        error-message="{{ $errors->first('cellphone') }}"
                        wire:model="cellphone"
                        x-mask="(99) 99999-9999"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="password"
                        label="Digite sua senha"
                        type="password"
                        required="true"
                        placeholder="Senha para acesso"
                        error-message="{{ $errors->first('password') }}"
                        wire:model="password"
                    />
                </div>

                <div class="col-span-12 md:col-span-6">
                    <x-input
                        name="password_confirmation"
                        label="Confirme sua senha"
                        type="password"
                        required="true"
                        placeholder="Repita sua senha"
                        error-message="{{ $errors->first('passwordConfirmation') }}"
                        wire:model="passwordConfirmation"
                    />
                </div>

                @error('form')
                    <span class="col-span-12 text-red-500 text-[13px] my-1 block">{{ $message }}</span>
                @enderror

                <button wire:loading.attr="disabled" type="submit" class="relative bg-blue-button w-full font-bold text-white-color rounded-[5px] py-2 transition-all duration-200 active:scale-[0.99] flex flex-row justify-center col-span-12">
                    <span>Criar minha conta</span>
                    <div wire:loading wire:target="save" class="absolute top-0 left-0 w-full h-full z-10">
                        <div class="w-full h-full rounded-md flex items-center justify-center bg-white/60">
                            <x-heroicon-s-arrow-path class="text-blue-link w-5 h-5 animate-spin"/>
                        </div>
                    </div>
                </button>
            </div>
        </form>
        <div class="flex flex-col gap-3 mt-4">
            <div class="flex flex-row justify-center text-[15px]">
                <p class="text-gray-700 font-medium ">
                    Já possuí uma conta? <a href="{{ route('login') }}" wire:navigate class="text-blue-link font-medium cursor-pointer hover:underline">Fazer Login</a>
                </p>
            </div>

            <div class="flex flex-row items-center w-full px-2 my-1">
                <span class="border-b border-[#747775a9] flex-grow"></span>
            </div>

            <div class="mt-3">
                <p class="text-gray-700 font-medium text-[13.5px] text-center">
                    Ao continuar, você concorda com os <span
                        class="text-blue-link hover:underline cursor-pointer">Termos</span> e <span
                        class="text-blue-link hover:underline cursor-pointer">Política de
                    Privacidade</span>.
                </p>
            </div>
        </div>
    </div>
</div>
