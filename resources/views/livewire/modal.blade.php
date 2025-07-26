<div x-data="{ modal: false }"
     x-on:active-modal-component-changed.window="modal = true"
     x-on:close-modal.window="modal = false"
     class="relative z-50">

    <div x-cloak x-show="modal"
         x-transition.opacity
         @click="modal = false; $wire.closeModal()"
         class="fixed inset-0 bg-black/50">
    </div>

    <aside x-cloak x-show="modal"
           x-transition:enter="transition transform duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed inset-y-0 right-0 z-50 w-[650px] min-h-screen bg-white shadow-xl"
           @keydown.escape.window="modal = false; $wire.closeModal()">

        <div class="p-4 border-b flex justify-between items-center">
            <h1 class="text-lg font-bold">Modal</h1>
            <button @click="modal = false; $wire.closeModal()" class="text-gray-600 text-xl">×</button>
        </div>


        <div class="overflow-y-auto max-h-[calc(100vh-64px)] p-4 cst-scrollbar">
            @forelse($components as $id => $component)
                @if($activeUuidComponent === $id)
                    @livewire($component['name'], $component['arguments'], key($id))
                @endif
            @empty
                <p class="text-gray-500">Nenhum componente ativo</p>
            @endforelse
        </div>

    </aside>
</div>
