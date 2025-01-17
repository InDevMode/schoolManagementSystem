<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function changePassword(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Modifiez votre mot de passe";
        return view('profile.change_password', $data);
    }

    public function updatePassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = User::getSingle(Auth::user()->id);

            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return redirect()->back()->with('success', 'Votre mot de passe a été modifié avec succès.');
            } else {
                return redirect()->back()->with('error', "Votre ancien mot de passe n'est pas correct.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification du mot de passe : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }
}
