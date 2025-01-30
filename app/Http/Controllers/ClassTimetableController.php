<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassTimetableModel;
use Illuminate\Http\Request;

class ClassTimetableController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des horaires de cours";
        $data['getClass'] = ClassModel::getClass();
        $data['getClassTimetable'] = ClassTimetableModel::getClassTimetable(10);
        return view('admin.class_timetable.list', $data);
    }

    public function add()
    {
    }

    public function create()
    {
    }

    public function edit()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
