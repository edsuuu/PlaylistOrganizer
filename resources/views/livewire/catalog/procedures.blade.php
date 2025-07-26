<div class="flex flex-col gap-4">
    <div class="flex flex-row justify-between">
        <h1 class="text-2xl font-bold">Procedimentos</h1>

        <div class="flex flex-row gap-4 items-center">
            <a x-on:click="$dispatch('open-side-modal', { componentName: 'scheduling.catalog.components.procedure.table-category', params: {}, events:[] })"
               class="w-full md:w-32 text-center cursor-pointer block px-2 py-1 text-xs bg-blue-black text-white rounded hover:bg-opacity-50 font-bold active:scale-[0.99]">Ver
                Categorias</a>
            <a x-on:click="$dispatch('open-side-modal', { componentName: 'scheduling.catalog.components.procedure.form-procedure', params: {}, events:[] })"
               class="w-full md:w-36 text-center cursor-pointer block px-2 py-1 text-xs bg-blue-black text-white rounded hover:bg-opacity-50 font-bold active:scale-[0.99]">Criar
                Procedimento</a>
        </div>
    </div>

    <div>

        <div class="flex flex-row justify-between ">
            <div class="flex flex-row gap-2">
                <div class="text-black flex flex-col text-[13px] w-[300px]">
                    <input type="search" name="search" placeholder="Nome do Procedimento ou Categoria"
                           wire:model.change.blur="searchProcedure"
                           class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link"
                           maxlength="20">
                </div>

                <div class="text-gray-500 text-[14px] flex flex-col w-[300px]">
                    <select name="categories" id="categories"
                            class="border border-gray-300 outline-none p-2 pl-3 rounded focus:border-blue-link "
                            wire:model.change="filterByCategory">
                        <option value="">Filtro por Categoria</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <button class="bg-gray-200 border text-sm border-gray-400 rounded px-3 active:scale-[0.99]" wire:click="clearFilters">Limpar filtros</button>
            </div>
        </div>

        <div class="shadow-lg sm:rounded-lg mt-5 bg-white">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="border-b p-2 ">
                <th class="text-left p-2 pl-4">Foto</th>
                <th class="text-center p-2 pl-4">Nome</th>
                <th class="text-center p-2">Descrição</th>
                <th class="text-center p-2">Preço</th>
                <th class="text-center p-2">Duração</th>
                <th class="text-center p-2">Categoria</th>
                <th class="text-center  p-2">Ação</th>
                </thead>
                <tbody>
                @foreach($procedures as $procedure)
                    <tr class="even:bg-gray-100 p-2">
                        <td class="text-left p-2 pl-4">{{$procedure->id}}</td>
                        <td class="text-center p-2 pl-4">{{$procedure->name}}</td>
                        <td class="text-center p-2 pl-4 truncate w-[200px] max-w-[200px] overflow-hidden">{{$procedure->description}}</td>
                        <td class="text-center p-2 pl-4">R$ {{ number_format($procedure->price, 2,  ',' , '.') }}</td>
                        <td class="text-center p-2 pl-4">{{$procedure->duration}}h</td>
                        <td class="text-center p-2 pl-4">{{$procedure->category->name ?? 'Sem Categoria'}}</td>
                        <td class="text-center p-2 flex flex-row gap-2 justify-center">
                            <a x-on:click="$dispatch('open-side-modal', { componentName: 'scheduling.catalog.components.procedure.form-procedure', params: {'idProcedure': {{$procedure->id}}}, events:[] })"
                               class="w-full md:w-20 text-center cursor-pointer block px-2 py-1 text-xs bg-blue-black text-white rounded hover:bg-opacity-50 font-bold">Editar</a>
                            <a x-on:click="$dispatch('open-side-modal2', { componentName: 'scheduling.catalog.components.procedure.form-procedure', params: {idProcedure: null, idDelete: {{$procedure->id}} }, events:[] })"
                               class="w-full md:w-20 text-center cursor-pointer block px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-opacity-50 font-bold">Apagar</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $procedures->links() }}
        </div>

    </div>


</div>
