<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Modal extends Component
{
    public array $components;
    public ?string $activeUuidComponent;
    public ?string $title;

    #[On('OpenModalComponent')]
    public function openModal(string $component, array $arguments = [], string $title = 'Modal'): void
    {
        $this->title = $title;
        $id = md5($component . serialize($arguments));

        $this->components[$id] = [
            'name' => $component,
            'attributes' => $arguments,
            'arguments' => $arguments,
        ];

        $this->activeUuidComponent = $id;

        $this->dispatch('active-modal-component-changed', id: $id);
    }

    #[On('CloseModalComponent')]
    public function closeModal($events = []): void
    {
        $this->dispatchModalEvents($events);

        if ($this->activeUuidComponent) {
            $this->destroyComponent($this->activeUuidComponent);
            $this->activeUuidComponent = null;
        }

        $this->dispatch('close-modal');
    }

    private function dispatchModalEvents($events): void
    {
        foreach ($events as $event) {
//            if (is_array($event)) {
//                $this->dispatch($event, reset($event));
//            }

            $this->dispatch($event);
        }
    }

    private function destroyComponent($id): void
    {
        unset($this->components[$id]);
        $this->components = [];
        $this->activeUuidComponent = null;
    }

    public function render()
    {
        return view('livewire.modal');
    }
}
