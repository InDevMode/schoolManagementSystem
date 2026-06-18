<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Retourne les notifications non lues de l'utilisateur connecté (JSON).
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->data['type']    ?? 'info',
                'icon'       => $n->data['icon']    ?? 'bell',
                'color'      => $n->data['color']   ?? 'gray',
                'title'      => $n->data['title']   ?? 'Notification',
                'message'    => $n->data['message'] ?? '',
                'url'        => $n->data['url']     ?? null,
                'created_at' => $n->created_at->diffForHumans(),
                'read_at'    => $n->read_at,
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer une notification spécifique comme lue.
     */
    public function markRead(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'success'      => true,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllRead(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true, 'unread_count' => 0]);
    }

    /**
     * Supprimer une notification.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        $user->notifications()->where('id', $id)->delete();

        return response()->json([
            'success'      => true,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Supprimer toutes les notifications lues.
     */
    public function clearRead(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        $user->notifications()->whereNotNull('read_at')->delete();

        return response()->json(['success' => true]);
    }
}
