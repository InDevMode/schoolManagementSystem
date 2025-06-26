<?php

namespace App\Http\Controllers;

use App\Mail\SendMailUserMail;
use App\Models\CommunicateModel;
use App\Models\NoticeBoardMessageModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommunicateController extends Controller
{
    public function list()
    {
        $data['header_title'] = 'Message de notifications';
        $data['getNoticeBoard'] = CommunicateModel::getNoticeBoard(10);
        return view('admin.communicate.noticeboard.list', $data);
    }

    public function add()
    {
        $data['header_title'] = 'Créer un message de notification';
        return view('admin.communicate.noticeboard.add', $data);
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
                foreach ($request->message_to as $message_to) {
                    $noticeBoardMessage = new NoticeBoardMessageModel;
                    $noticeBoardMessage->communicates_id = $noticeBoard->id;
                    $noticeBoardMessage->message_to = $message_to;
                    $noticeBoardMessage->save();
                }
            }

            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce message de notification a été créé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un  message de notification : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function edit($id)
    {
        $data['getNoticeBoard'] = CommunicateModel::getSingle($id);
        $data['header_title'] = 'Modifier un message de notification';
        return view('admin.communicate.noticeboard.edit', $data);
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
            $noticeBoard->is_delete = 1;
            $noticeBoard->save();

            NoticeBoardMessageModel::deleteNoticeBoardMessage($id);
            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce  message de notification a été supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression d'un  message de notification : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function myNoticeBoard()
    {
        $data['header_title'] = 'Mes notifications';
        $data['getStudentNoticeboard'] = CommunicateModel::getNoticeBoardWithUserType(Auth::user()->user_type, 5);
        return view('student.notice_board', $data);
    }

    public function teacherNoticeBoard()
    {
        $data['header_title'] = 'Mes notifications';
        $data['getTeacherNoticeboard'] = CommunicateModel::getNoticeBoardWithUserType(Auth::user()->user_type, 5);
        return view('teacher.notice_board', $data);
    }

    public function parentNoticeBoard()
    {
        $data['header_title'] = 'Mes notifications';
        $data['getParentNoticeboard'] = CommunicateModel::getNoticeBoardWithUserType(Auth::user()->user_type, 5);
        return view('parent.notice_board', $data);
    }

    public function sendMail()
    {
        $data['header_title'] = 'Envoyer un mail';
        $data['getUsers'] = User::getUsers();
        return view('admin.communicate.send_mail', $data);
    }

    public function sendMailCreate(Request $request)
    {
        try {
            if (!empty($request->user_id)) {
                foreach ($request->user_id as $userId) {
                    $user = User::getSingle($userId);

                    if ($user && $user->email) {
                        // Ajouter dynamiquement les données nécessaires pour l'email
                        $user->send_message = $request->message;
                        $user->send_subject = $request->subject;
                        // Envoi du mail
                        Mail::to($user->email)->send(new SendMailUserMail($user));
                    }

                    if (!empty($request->message_to)) {
                        foreach ($request->message_to as $user_type) {
                            $getUser = User::getUserByUserType($user_type);
                            if (!empty($getUser)) {
                                foreach ($getUser as $user) {
                                    if ($user && $user->email) {
                                        // Ajouter dynamiquement les données nécessaires pour l'email
                                        $user->send_message = $request->message;
                                        $user->send_subject = $request->subject;
                                        // Envoi du mail
                                        Mail::to($user->email)->send(new SendMailUserMail($user));
                                    }
                                }
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
