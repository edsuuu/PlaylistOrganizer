<div>
    <div class="flex justify-between w-full mb-4">
        <div>
            <h2 class="font-semibold text-2xl text-blue-black mt-1 leading-tight">Categorias</h2>
        </div>
    </div>


    <div class="flex flex-row justify-between">
        <a x-on:click="$dispatch('open-side-modal2', { componentName: 'scheduling.catalog.components.procedure.form-category', params: {}, events:[] })"
           class="w-full md:w-40 text-center cursor-pointer block px-2 py-1 text-xs bg-blue-black text-white rounded hover:bg-opacity-50 font-bold active:scale-[0.99]">Criar
            nova Categoria</a>
    </div>

    <div class="w-full flex flex-row gap-5">
        <div class="text-black flex flex-col gap-0.5 mt-4  w-full">
            <input type="search" name="search" placeholder="Nome da categoria"
                   wire:model.change.blur="search"
                   class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link"
                   maxlength="20">
        </div>
    </div>

    <div class="shadow-lg sm:rounded-lg mt-5 bg-white">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="border-b p-2 ">
            <th class="text-left p-2 pl-4">Nome</th>
            <th class="text-center p-2">Procedimentos vinculados</th>
            <th class="text-center p-2">Ação</th>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr class="even:bg-gray-100 p-2">
                    <td class="text-left p-2 pl-4">{{$category->name}}</td>
                    <td class="text-center p-2 pl-4">{{$category->procedures->count()}}</td>
{{--                    <td class="text-center p-2">{{$category->products->count()}}</td>--}}
                    <td class="text-center p-2 flex flex-row gap-2 justify-center">
                        <a x-on:click="$dispatch('open-side-modal2', { componentName: 'scheduling.catalog.components.procedure.form-category', params: {'categoryId': {{$category->id}}}, events:[] })"
                           class="w-full md:w-20 text-center cursor-pointer block px-2 py-1 text-xs bg-blue-black text-white rounded hover:bg-opacity-50 font-bold">Editar</a>
                        <a x-on:click="$dispatch('open-side-modal2', { componentName: 'scheduling.catalog.components.procedure.form-category', params: {categoryId: null, categoryIdDelete: {{$category->id}} }, events:[] })"
                           class="w-full md:w-20 text-center cursor-pointer block px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-opacity-50 font-bold">Apagar</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>


</div>
