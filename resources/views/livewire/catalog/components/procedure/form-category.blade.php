<div>

    <div class="flex justify-between w-full mb-4">
        <div>
            @if ($idCategoryDelete)
                <h2 class="font-semibold text-2xl text-blue-black mt-1 leading-tight">Apagar Categoria</h2>
            @else
                <h2 class="font-semibold text-2xl text-blue-black mt-1 leading-tight">{{ $idCategory ? 'Editar Categoria' : 'Criar Categoria' }}</h2>
            @endif
        </div>
    </div>

    <div class="flex flex-col gap-4 bg-white p-4 shadow-lg rounded-lg">
        @if($idCategoryDelete)
            <h2 class="font-semibold text-sm text-black mt-1 leading-tight">Deseja mesmo apagar essa categoria ?</h2>
            <p>Após apagar, todos produtos que estiver com essa categoria será atribuido com "Sem Categoria"</p>


            <div class="w-full p-2 justify-center items-center">
                <button class="border border-gray-200 rounded-lg bg-red-500 text-white font-semibold px-5 py-1" wire:click="deleteCategory">Sim</button>
                <button class="border border-gray-500 bg-gray-200 rounded-lg text-black font-semibold px-5 py-1" wire:click="$dispatch('close-side-modal2')">Não</button>
            </div>
        @else
            <form wire:submit.prevent="save">
                @csrf

                <div class="text-black flex flex-col gap-0.5">
                    <p class="text-gray-700 text-[14px] font-medium">Nome da categoria</p>
                    <input type="text" name="name" placeholder="Nome da nova categoria"
                           wire:model="name"
                           class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('name') border-red-500 @enderror"
                           maxlength="20">
                    @error('name')
                    <span class="text-red-500 text-[13px]">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-blue-black p-2 rounded-2xl text-white font-semibold active:scale-[0.99] mt-4">
                    Salvar
                </button>
            </form>
        @endif

    </div>
</div>
