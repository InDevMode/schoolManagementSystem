<?php

namespace App\Http\Controllers;

use App\Models\CommunicateModel;
use App\Models\NoticeBoardMessageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommunicateController extends Controller
{
    public function list()
    {
        $data['header_title'] = 'Message de notification';
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
                    $noticeBoardMessage->created_by = auth()->user()->id;
                    $noticeBoardMessage->save();
                }
            }

            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce message a été créé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un message : " . $e->getMessage());

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
                redirect()->back()->with('error', 'Ce message n\existe pas');
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

            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce message a été modifié avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour d'un message : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function delete($id)
    {
        try {
            $noticeBoard = CommunicateModel::getSingle($id);
            $noticeBoard->delete();

            NoticeBoardMessageModel::deleteNoticeBoardMessage($id);
            return redirect('admin/communicate/noticeboard/list')->with('success', 'Ce message a été supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression d'un message : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function myNoticeBoard(){
        $data['header_title'] = 'Mes notifications';
        $data['getStudentNoticeboard'] = CommunicateModel::getStudentNoticeBoard(3, 5);
        return view('student.notice_board', $data);
    }

}
