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
            $getChats  = ChatModel::getChats($receiver_id, $sender_id)->map(function ($m) {
                $arr             = $m->toArray();
                $arr['file_url'] = $m->file ? url('upload/chats/' . $m->file) : null;
                return $arr;
            })->values();
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

    // ── API JSON pour le polling temps réel ──────────────────────────────────

    /**
     * Retourne les messages depuis un ID donné (pour le polling).
     */
    public function pollMessages(Request $request)
    {
        $sender_id   = Auth::id();
        $receiver_id = (int) $request->receiver_id;
        $lastId      = (int) ($request->last_id ?? 0);

        if (!$receiver_id) {
            return response()->json(['messages' => [], 'contacts' => []]);
        }

        // Tous les messages de la conversation pour les nouveaux + les mises à jour d'état
        $allMessages = ChatModel::getChats($receiver_id, $sender_id);

        // Nouveaux messages (jamais reçus)
        $newMessages = $allMessages
            ->filter(fn($m) => $m->id > $lastId)
            ->map(function ($m) {
                $arr             = $m->toArray();
                $arr['file_url'] = $m->file ? url('upload/chats/' . $m->file) : null;
                return $arr;
            })
            ->values();

        // Messages existants dont l'état a changé (supprimés côté serveur)
        $updatedMessages = $allMessages
            ->filter(fn($m) => $m->id <= $lastId && $m->is_delete == 1)
            ->map(fn($m) => ['id' => $m->id, 'is_delete' => 1])
            ->values();

        // Marquer comme lus les messages reçus de cet expéditeur
        ChatModel::updateCountMessage($sender_id, $receiver_id);

        // ID du dernier message envoyé par moi et qui est maintenant lu
        $readUpTo = ChatModel::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)
            ->where('status', '>=', 1)
            ->where('is_delete', 0)
            ->max('id');

        $contacts = ChatModel::getChatUser($sender_id);

        return response()->json([
            'messages'         => $newMessages,
            'updated_messages' => $updatedMessages,
            'contacts'         => $contacts,
            'read_up_to'       => $readUpTo,
        ]);
    }

    /**
     * Retourne uniquement les contacts (pour polling de la sidebar).
     */
    public function pollContacts(Request $request)
    {
        $sender_id = Auth::id();
        User::updateLastLogin($sender_id);

        return response()->json([
            'contacts' => ChatModel::getChatUser($sender_id),
        ]);
    }

    /**
     * Envoi d'un message via AJAX (pas de redirect).
     */
    public function sendMessageAjax(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:5000',
                'file'    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,zip,rar,txt,mp4,mov,avi,mkv|max:20480',
            ]);

            // Il faut au moins un message ou un fichier
            if (empty($request->message) && !$request->hasFile('file')) {
                return response()->json(['success' => false, 'message' => 'Message ou fichier requis.'], 422);
            }

            $chat = new ChatModel();
            $chat->sender_id    = Auth::id();
            $chat->receiver_id  = $request->receiver_id;
            $chat->message      = $request->message ?? '';
            $chat->created_date = Carbon::now();

            if ($request->hasFile('file')) {
                $file         = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $fileName     = 'chat_file' . date('dmYhis') . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move('upload/chats/', $fileName);
                $chat->file = $fileName;
            } else {
                $originalName = null;
            }

            $chat->save();

            // Ajouter l'URL du fichier dans la réponse
            $chatData              = $chat->toArray();
            $chatData['file_url']  = $chat->file ? url('upload/chats/' . $chat->file) : null;
            $chatData['file_name'] = $chat->file ? ($originalName ?? $chat->file) : null;

            return response()->json(['success' => true, 'message' => $chatData]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        } catch (\Exception $e) {
            Log::error('sendMessageAjax: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi.'], 500);
        }
    }

    /**
     * Modification d'un message via AJAX.
     */
    public function updateMessageAjax(Request $request, $id)
    {
        try {
            $chat = ChatModel::getSingle($id);
            if (!$chat || $chat->sender_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
            }
            $chat->message      = $request->message;
            $chat->created_date = Carbon::now();
            $chat->save();
            return response()->json(['success' => true, 'message' => $chat]);
        } catch (\Exception $e) {
            Log::error('updateMessageAjax: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur.'], 500);
        }
    }

    /**
     * Suppression d'un message via AJAX.
     */
    public function deleteMessageAjax(Request $request, $id)
    {
        try {
            $chat = ChatModel::getSingle($id);
            if (!$chat || $chat->sender_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
            }
            $chat->is_delete = 1;
            $chat->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('deleteMessageAjax: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur.'], 500);
        }
    }

    /**
     * Signal "en train d'écrire" — stocké 4s dans le cache.
     */
    public function setTyping(Request $request)
    {
        $senderId    = Auth::id();
        $receiverId  = (int) $request->receiver_id;
        $isTyping    = (bool) $request->is_typing;

        $key = "chat_typing_{$senderId}_{$receiverId}";

        if ($isTyping) {
            \Illuminate\Support\Facades\Cache::put($key, true, now()->addSeconds(4));
        } else {
            \Illuminate\Support\Facades\Cache::forget($key);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Vérifie si l'interlocuteur est en train d'écrire.
     * Retourne aussi le statut "lu" du dernier message envoyé.
     */
    public function checkTyping(Request $request)
    {
        $myId        = Auth::id();
        $receiverId  = (int) $request->receiver_id;

        // Est-ce que le destinataire est en train de m'écrire ?
        $typingKey   = "chat_typing_{$receiverId}_{$myId}";
        $isTyping    = \Illuminate\Support\Facades\Cache::has($typingKey);

        // Statut du dernier message que j'ai envoyé à ce destinataire
        $lastMsg = ChatModel::where('sender_id', $myId)
            ->where('receiver_id', $receiverId)
            ->where('is_delete', 0)
            ->orderBy('id', 'desc')
            ->first(['id', 'status']);

        return response()->json([
            'is_typing'      => $isTyping,
            'last_msg_id'    => $lastMsg?->id,
            'last_msg_read'  => $lastMsg ? ($lastMsg->status >= 1) : false,
        ]);
    }

}
