<?php

namespace App\Http\Controllers;

use App\Models\ChatModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{

    public function chat(Request $request)
    {
        $data['header_title'] = "Mes messages";
        $sender_id = Auth::user()->id;
        if (!empty($request->receiver_id)) {
            $receiver_id = base64_decode($request->receiver_id);
            if ($receiver_id == $sender_id) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas vous envoyer un message');
            }
            $data['getReceiver'] = User::getSingle($receiver_id);
            $data['getChats'] = ChatModel::getChats($receiver_id, $sender_id);
            $data['getChatUser'] = ChatModel::getChatUser($receiver_id);
            // dd($data['getChatUser']);
        }
        return view('chat.list', $data);
    }

    public function sendMessage(Request $request)
    {
        try {

            $chat = new ChatModel();
            $chat->sender_id = Auth::user()->id;
            $chat->receiver_id = $request->receiver_id;
            $chat->message = $request->message;
            $chat->created_date = Carbon::createFromTimestamp(time());
            $chat->save();

            return redirect()->back()->with('success', 'Message envoyé.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un message : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function markAsRead(Request $request)
    {
        $receiverId = base64_decode($request->receiver_id);
        $chat = ChatModel::getMarkAsRead($receiverId);
        if ($chat) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }


}
