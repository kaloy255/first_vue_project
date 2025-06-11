<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
   public function index(Request $request)
{
    $search    = $request->input('search');
    $sort      = $request->input('sort', 'created_at');
    $direction = $request->input('direction', 'desc');

    $myTasks = Tasks::with('creator')
        ->where('assign_to', auth()->id())
        ->when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
        )
        ->orderBy($sort, $direction)
        ->paginate(5)                // ← paginate instead of get()
        ->withQueryString();          // ← keep ?search=…&sort=… on links

    return Inertia::render('Dashboard', [
        'myTasks' => $myTasks,
        'filters' => compact('search', 'sort', 'direction'),
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
