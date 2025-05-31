<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
   public function index(Request $request)
{
    $sort = $request->input('sort', 'created_at'); 
    $direction = $request->input('direction', 'desc'); 

    $myTasks = Tasks::with('creator')
        ->where('assign_to', auth()->id())
        ->orderBy($sort, $direction)
        ->get();

    return Inertia::render('Dashboard', [
        'myTasks' => $myTasks,
        'filters' => [
            'sort' => $sort,
            'direction' => $direction,
        ],
    ]);
}

    public function startTask(Tasks $task)
    {
        $task->status = 'inprogress';
        $task->save();

        return redirect()->back();
    }
    public function checkTask(Tasks $task)
    {
       
        if ($task->assign_to !== auth()->id()) {
            abort(403);
        }

        if($task->status == 'inprogress'){
            $task->status = 'checking';
            $task->save();

            return redirect()->back();
        }else{
             return redirect()->back();
        }

       
    }
}
