<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Livewire\Component;

class SessionsProfile extends Component
{
    public $sessions = [];

    public function mount()
    {
        $this->sessions = $this->getSessions();
    }

    public function getSessions(): array
    {
        return collect(
            DB::table('sessions')
                ->where('user_id', Auth::id())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            $agent = $this->createAgent($session);

            return [
                'platform' => $agent->platform() === 'OS X' ? 'MacOS' : ($agent->platform() ?: 'Desconhecido'),
                'browser' => $agent->browser() ?: 'Desconhecido',
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'is_desktop' => $agent->isDesktop(),
            ];
        })->toArray();
    }

    private function createAgent($session)
    {
        return tap(new Agent(), fn($agent) => $agent->setUserAgent($session->user_agent));
    }

    public function render()
    {
        return view('livewire.profile.sessions-profile');
    }
}
