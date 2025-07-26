<div>
    <div class="flex justify-between w-full mb-4">
        <div>
            @if ($idDelete)
                <h2 class="font-semibold text-2xl text-blue-black mt-1 leading-tight">Apagar Produto</h2>
            @else
                <h2 class="font-semibold text-2xl text-blue-black mt-1 leading-tight">{{ $id ? 'Editar Produto' : 'Criar Produto' }}</h2>
            @endif
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-lg">
        @if(!$idDelete)

            <form wire:submit.prevent="save" class="flex flex-col gap-4">
                @csrf

                <div class="text-black flex flex-col gap-0.5">
                    <p class="text-gray-700 text-[14px] font-medium">Nome do Produto</p>
                    <input type="text" name="name" placeholder="Nome da nova Produto"
                           wire:model="name"
                           class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('name') border-red-500 @enderror"
                           maxlength="20">
                    @error('name')
                    <span class="text-red-500 text-[13px]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-black flex flex-col gap-0.5">
                    <p class="text-gray-700 text-[14px] font-medium">Preço (R$)</p>
                    <input type="text" name="name" placeholder="Preço"
                           oninput="this.value = formatMoney(this.value, 10)" wire:model="price"
                           class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('name') border-red-500 @enderror"
                           maxlength="20">
                    @error('name')
                    <span class="text-red-500 text-[13px]">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-black flex flex-col gap-0.5">
                    <p class="text-gray-700 text-[14px] font-medium">Quantidade</p>
                    <input type="number" name="quantidade" placeholder="Quantidade"
                           wire:model="quantity"
                           class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('quantity') border-red-500 @enderror" min="0"   oninput="if (this.value.length > 7) this.value = this.value.slice(0, 7);">
                    @error('quantity')
                    <span class="text-red-500 text-[13px]">{{ $message }}</span>
                    @enderror
                </div>


                @if(count($categories) >= 1)
                    <div class="text-black flex flex-col gap-0.5">
                        <p class="text-gray-700 text-[14px] font-medium">Categoria</p>
                        <select name="categories" id="categories" class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link  @error('category') border-red-500 @enderror"
                                wire:model="categorySelect">
                            <option value="">Selecione uma Categoria</option>
                            @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <span class="text-red-500 text-[13px]">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <h1>Você ainda não tem categoria, Deseja criar ?</h1>
                        <a x-on:click="$dispatch('open-side-modal2', { componentName: 'scheduling.catalog.components.product.form-category', params: {}, events:[] })"
                           class="w-full md:w-40 text-center cursor-pointer block px-2 py-1 text-xs bg-blue-black text-white rounded hover:bg-opacity-50 font-bold active:scale-[0.99]">Criar nova Categoria</a>
                    </div>
                @endif

                <button type="submit"
                        class="w-full bg-blue-black p-2 rounded-2xl text-white font-semibold active:scale-[0.99] mt-4">
                    Salvar
                </button>
            </form>
        @else
            <h2 class="font-semibold text-sm text-black mt-1 leading-tight">Deseja mesmo apagar esse produto ?</h2>

            <div class="w-full p-2 justify-center items-center">
                <button class="border border-gray-200 rounded-lg bg-red-500 text-white font-semibold px-5 py-1" wire:click="deleteProduct">Sim</button>
                <button class="border border-gray-500 bg-gray-200 rounded-lg text-black font-semibold px-5 py-1" wire:click="$dispatch('close-side-modal2')">Não</button>
            </div>
        @endif
    </div>
</div>



