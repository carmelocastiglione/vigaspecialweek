<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Classroom;

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
            $data['classesCount'] = 0;     // TODO: implementare
        }

        return view('dashboard', $data);
    }
}