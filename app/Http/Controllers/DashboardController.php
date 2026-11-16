<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $data = [];

        // Esegui query costose solo se l'utente è admin
        if (auth()->user()->hasRole('admin')) {
            $data['usersCount'] = User::count();
            $data['departmentsCount'] = Department::count();
            $data['categoriesCount'] = Category::count();
            $data['subjectsCount'] = Subject::count();
            $data['classroomsCount'] = Classroom::count();
            $data['schoolClassesCount'] = SchoolClass::count();
            $data['activitiesCount'] = Activity::count();
            $data['assignmentsCount'] = TeachingAssignment::count();
            $data['likesCount'] = DB::table('activity_likes')->count();
        }

        return view('dashboard', $data);
    }
}
