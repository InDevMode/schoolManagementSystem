<?php

namespace App\Http\Controllers;

use App\Models\ChatModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{

    public function chat(Request $request)
    {
        $data['header_title'] = "Mes messages";
        $sender_id = Auth::user()->id;

        // Mise à jour du last_login à chaque chargement de la page
        User::where('id', $sender_id)->update([
            'last_login' => now(),
        ]);

        // Récupération de la liste des contacts (toujours utile)
        $data['getChatUser'] = ChatModel::getChatUser($sender_id);

        // Initialiser receiver et chats seulement si receiver_id existe
        if (!empty($request->receiver_id)) {
            $receiver_id = base64_decode($request->receiver_id);

            if ($receiver_id == $sender_id) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas vous envoyer un message');
            }

            ChatModel::updateCountMessage($sender_id, $receiver_id);

            $data['getReceiver'] = User::getSingle($receiver_id);
            $data['getChats'] = ChatModel::getChats($receiver_id, $sender_id);
        }

        return view('chat.list', $data);
    }

    public function sendMessage(Request $request)
    {
        // dd($request->all());
        try {

            $chat = new ChatModel();
            $chat->sender_id = Auth::user()->id;
            $chat->receiver_id = $request->receiver_id;
            $chat->message = $request->message;
            $chat->created_date = Carbon::createFromTimestamp(time());

            if (!empty($request->file('file'))) {
                $ext = $request->file('file')->getClientOriginalExtension();
                $file = $request->file('file');
                $randomStr = 'chat_file' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/chats/', $fileName);
                $chat->file = $fileName;
            }

            $chat->save();

            return redirect()->back()->with('success', 'Message envoyé.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un message : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }


}
