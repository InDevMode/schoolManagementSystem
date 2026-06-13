<?php

namespace App\Http\Controllers;

use App\Mail\SendMailUserMail;
use App\Models\CommunicateModel;
use App\Models\NoticeBoardMessageModel;
use App\Models\User;
use App\Notifications\MailSentNotification;
use App\Notifications\NoticeBoardNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class CommunicateController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Noticeboard/Index', [
            'notices' => CommunicateModel::getNoticeBoardWithRecipients(12),
        ]);
    }

    public function create(Request $request)
    {

        try {

            $noticeBoard = new CommunicateModel;
            $noticeBoard->title = $request->title;
            $noticeBoard->notice_date = $request->notice_date;
            $noticeBoard->publish_date = $request->publish_date;
            $noticeBoard->message = $request->message;
            $noticeBoard->created_by = auth()->user()->id;
            $noticeBoard->save();

            if (!empty($request->message_to)) {
                $senderName = auth()->user()->name . ' ' . auth()->user()->last_name;
                $notification = new NoticeBoardNotification($noticeBoard->title, $senderName);

                foreach ($request->message_to as $message_to) {
                    $noticeBoardMessage = new NoticeBoardMessageModel;
                    $noticeBoardMessage->communicates_id = $noticeBoard->id;
                    $noticeBoardMessage->message_to = $message_to;
                    $noticeBoardMessage->save();

                    // Notification in-app pour chaque utilisateur du groupe ciblé
                    $recipients = User::getUserByUserType((int) $message_to);
                    foreach ($recipients as $recipient) {
                        $recipient->notify($notification);
                    }
                }
            }

            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce message de notification a été créé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un  message de notification : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function edit(int $id)
    {
        $notice = CommunicateModel::getSingleWithRecipients($id);
        abort_unless($notice, 404);
        return Inertia::render('Admin/Noticeboard/Index', [
            'notices'    => CommunicateModel::getNoticeBoardWithRecipients(12),
            'editNotice' => $notice,
        ]);
    }

    public function update(Request $request, $id)
    {

        try {

            $existingNoticeBoard = CommunicateModel::getSingle($id);
            if (!empty($data['getNoticeBoard'])) {
                redirect()->back()->with('error', 'Ce message de notification n\existe pas');
            }
            $existingNoticeBoard->title = $request->title;
            $existingNoticeBoard->notice_date = $request->notice_date;
            $existingNoticeBoard->publish_date = $request->publish_date;
            $existingNoticeBoard->message = $request->message;
            $existingNoticeBoard->save();

            NoticeBoardMessageModel::deleteNoticeBoardMessage($id);
            if (!empty($request->message_to)) {
                foreach ($request->message_to as $message_to) {
                    $noticeBoardMessage = new NoticeBoardMessageModel;
                    $noticeBoardMessage->communicates_id = $existingNoticeBoard->id;
                    $noticeBoardMessage->message_to = $message_to;
                    $noticeBoardMessage->save();
                }
            }

            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce  message de notification a été modifié avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour d'un  message de notification : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function delete($id)
    {
        try {
            $noticeBoard = CommunicateModel::getSingle($id);
            abort_unless($noticeBoard, 404);
            $noticeBoard->is_delete = 1;
            $noticeBoard->deleted_at = now();
            $noticeBoard->save();

            NoticeBoardMessageModel::deleteNoticeBoardMessage($id);
            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce message de notification a été supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression d'un message de notification : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    /**
     * Activer / désactiver une notification
     */
    public function toggle($id)
    {
        try {
            $noticeBoard = CommunicateModel::getSingle($id);
            abort_unless($noticeBoard, 404);
            $noticeBoard->is_active = $noticeBoard->is_active ? 0 : 1;
            $noticeBoard->save();

            $label = $noticeBoard->is_active ? 'activée' : 'désactivée';
            return redirect()->back()->with('success', "La notification a été $label avec succès.");
        } catch (\Exception $e) {
            Log::error("Erreur lors du toggle d'une notification : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Historique des notifications supprimées
     */
    public function history()
    {
        return Inertia::render('Admin/Noticeboard/History', [
            'deleted' => CommunicateModel::getDeletedNoticeBoard(15),
        ]);
    }

    /**
     * Restaurer une notification supprimée
     */
    public function restore($id)
    {
        try {
            $noticeBoard = CommunicateModel::find($id);
            abort_unless($noticeBoard && $noticeBoard->is_delete == 1, 404);
            $noticeBoard->is_delete = 0;
            $noticeBoard->deleted_at = null;
            $noticeBoard->save();

            return redirect()->back()->with('success', 'La notification a été restaurée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la restauration d'une notification : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function myNoticeBoard()
    {
        return Inertia::render('Student/Noticeboard/Index', [
            'notices' => CommunicateModel::getNoticeBoardWithUserType(Auth::user()->user_type, 15),
        ]);
    }

    public function teacherNoticeBoard()
    {
        return Inertia::render('Teacher/Noticeboard/Index', [
            'notices' => CommunicateModel::getNoticeBoardWithUserType(Auth::user()->user_type, 15),
        ]);
    }

    public function parentNoticeBoard()
    {
        return Inertia::render('Parent/Noticeboard/Index', [
            'notices' => CommunicateModel::getNoticeBoardWithUserType(Auth::user()->user_type, 15),
        ]);
    }

    public function sendMail()
    {
        return Inertia::render('Admin/SendMail/Index', [
            'users' => User::getUsers(),
        ]);
    }

    public function sendMailCreate(Request $request)
    {
        try {
            $senderName = auth()->user()->name . ' ' . auth()->user()->last_name;
            $notification = new MailSentNotification($request->subject, $senderName);

            // Envoi aux destinataires individuels
            if (!empty($request->user_ids)) {
                foreach ($request->user_ids as $userId) {
                    $user = User::getSingle($userId);

                    if ($user && $user->email) {
                        $user->send_message = $request->message;
                        $user->send_subject = $request->subject;
                        Mail::to($user->email)->send(new SendMailUserMail($user));

                        // Notification in-app
                        $user->notify($notification);
                    }
                }
            }

            // Envoi aux groupes (indépendant des destinataires individuels)
            if (!empty($request->message_to)) {
                foreach ($request->message_to as $user_type) {
                    $groupUsers = User::getUserByUserType($user_type);
                    if (!empty($groupUsers)) {
                        foreach ($groupUsers as $user) {
                            if ($user && $user->email) {
                                $user->send_message = $request->message;
                                $user->send_subject = $request->subject;
                                Mail::to($user->email)->send(new SendMailUserMail($user));

                                // Notification in-app
                                $user->notify($notification);
                            }
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Les mails ont été envoyés avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi du mail : " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi. Veuillez réessayer.');
        }
    }

}
