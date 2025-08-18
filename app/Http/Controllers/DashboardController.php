<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\CommunicateModel;
use App\Models\ExaminationModel;
use App\Models\FeesCollectionModel;
use App\Models\SubjectModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['header_title'] = 'Tableau de bord';
        switch (Auth::user()->user_type) {
            case 1:

                $data['totalUser'] = User::getTotalUser();
                $data['totalAdmin'] = User::getTotalUserWithUserType(1);
                $data['totalTeacher'] = User::getTotalUserWithUserType(2);
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                $data['totalParent'] = User::getTotalUserWithUserType(4);
                $data['totalClass'] = ClassModel::getTotalClass();
                $data['totalSubject'] = SubjectModel::getTotalSubject();
                $data['totalFeesCollections'] = FeesCollectionModel::getTotalFeesCollections();
                $data['totalCommunicate'] = CommunicateModel::getTotalCommunicate();
                $data['totalExam'] = ExaminationModel::getTotalExam();
                $data['totalFeesCollectionsToday'] = FeesCollectionModel::getTotalFeesCollectionsToday();

                return view('admin.dashboard', $data);

            case 2:
                $data['totalStudent'] = User::getTotalUserWithUserType(3);
                return view('teacher.dashboard', $data);

            case 3:
                $data['totalStudent'] = User::getTotalUserWithUserType(3);;
                return view('student.dashboard', $data);

            case 4:
                $data['totalParent'] = User::getTotalUserWithUserType(4);
                return view('parent.dashboard', $data);

        }
        return view('auth.login');
    }
}
