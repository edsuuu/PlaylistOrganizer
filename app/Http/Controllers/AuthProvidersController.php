<?php

namespace App\Http\Controllers;

use App\Livewire\Actions\Logout;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthProvidersController extends Controller
{
    public function spotifyAuth()
    {
        return Socialite::driver('spotify')->redirect();
    }

    public static function login($userId, $domain, $remember = false)
    {
        $user = User::find($userId);
        Auth::login($user, $remember);
        return redirect()->route('dashboard');
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $userDB = User::query()
                ->with(['tenant.domain'])
                ->where('email', $googleUser->user['email'])
                ->orWhere('google_id', $googleUser->user['id'])
                ->first();

            if (!$userDB) {
                return redirect()->route('login')->withErrors(['google' => 'Erro ao autenticar com Google. Não existe conta com esse email vinculado.']);
            }

            if (!isset($userDB->google_id)) {
                $userDB->update([
                    'google_id' => $googleUser->user['id'],
                    'email_verified_at' => now(),
                ]);
            }

            if ($userDB->tenant === null) {
                Auth::login($userDB, true);
                // validar como vai funcionar o gerenciamento dos tentant
                return redirect()->route('dashboard');
            }

            $token = encrypt(['user_id' => $userDB->id, 'expires' => now()->addMinutes(4), 'remember' => true]);

            return redirect()->route('googleRedirectAuth', ['token' => $token]);
        } catch (ClientException $e) {
            Log::channel('daily')->error($e);
            return redirect()->route('login')->withErrors(['google' => 'Erro ao autenticar com Google. Por favor, tente novamente.']);
        } catch (\Exception $e) {
            Log::channel('daily')->error($e);
            return redirect()->route('login')->withErrors(['error' => 'Ocorreu um erro inesperado. Tente novamente.']);
        }
    }


    public function logout(Logout $logout)
    {
        $logout();
        return redirect('dashboard');
    }
}
