<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendanceStudent(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $data['header_title'] = 'Liste de présence des apprenants';
        $data['getClass'] = ClassModel::getClass();

        if (!empty($request->get('class_id')) && !empty($request->get('attendance_date'))) {
            $data['getStudent'] = User::getStudent($request->get('class_id'));
        }

        return view('admin.attendance.student.list', $data);
    }

}
