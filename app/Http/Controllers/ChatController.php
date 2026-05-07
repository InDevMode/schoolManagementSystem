<?php

namespace App\Http\Controllers;

use App\Models\ChatModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatController extends Controller
{

    public function chat(Request $request)
    {
        $sender_id = Auth::user()->id;
        User::updateLastLogin($sender_id);

        $getChatUser = ChatModel::getChatUser($sender_id);

        $receiver    = null;
        $getChats    = [];

        if (!empty($request->receiver_id)) {
            $receiver_id = base64_decode($request->receiver_id);

            if ($receiver_id == $sender_id) {
                return back()->with('error', 'Vous ne pouvez pas vous envoyer un message.');
            }

            ChatModel::updateCountMessage($sender_id, $receiver_id);
            $receiver  = User::getSingle($receiver_id);
            $getChats  = ChatModel::getChats($receiver_id, $sender_id);
        }

        return \Inertia\Inertia::render('Chat/Index', [
            'contacts'    => $getChatUser,
            'receiver'    => $receiver,
            'chats'       => $getChats,
            'receiver_id' => $request->receiver_id ?? null,
        ]);
    }

    public function sendMessage(Request $request)
    {
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

    public function updateMessage(Request $request, $id)
    {
        try {

            $chat = ChatModel::getSingle($id);
            if ($chat->sender_id !== Auth::user()->id) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas modifier ce message');
            }
            $chat->message = $request->message;
            $chat->created_date = Carbon::createFromTimestamp(time());

            if (!empty($request->file('file'))) {
                $chatFile = $chat->file;
                if (!empty($chatFile)) {
                    $chatFileUrl = ChatModel::getChatFile();
                    if (!empty($chatFileUrl)) {
                        unlink('upload/chats/' . $chatFile);
                    }
                }
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

    public function deleteMessage($id)
    {
        $chat = ChatModel::getSingle($id);
        if ($chat->sender_id !== Auth::user()->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer ce message');
        }
        $chat->is_delete = 1;
        $chat->save();
        return redirect()->back()->with('success', 'Message supprimé');
    }

}
