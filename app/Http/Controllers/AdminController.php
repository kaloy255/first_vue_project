<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
   public function index(Request $request)
{
    $searchUser = $request->input('search');
    $currentUserId = $request->user()->id;

    // Sorting
    $sortField = $request->input('sort', 'name');
    $sortDirection = $request->input('direction', 'asc');

    // Fetch tasks with sorting
    $usersTasks = Tasks::with(['assign_to', 'creator'])
        ->orderBy($sortField, $sortDirection)
        ->paginate(10)
        ->appends($request->only(['search', 'sort', 'direction'])); 

    $users = User::query()
        ->when($searchUser, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })
        ->where('id', '!=', $currentUserId)
        ->get(['id', 'name', 'email']);

    return Inertia::render('Admin/Dashboard', [
        'users' => $users,
        'usersTasks' => $usersTasks,
        'filters' => [
            'sort' => $sortField,
            'direction' => $sortDirection,
        ]
    ]);
}


    public function store(Request $request){
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'selectedUser' => 'required',
        ]);

        Tasks::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'assign_to' => $request->input('selectedUser'),
            'status' => 'pending',
            'created_by' => auth()->user()->id
        ]);


         // Redirect to the same page
        $page = $request->input('page', 1);
        return redirect()->route('admin.dashboard', ['page' => $page]);
    }

      public function doneTask(Tasks $task)
    {
        $task->status = 'completed';
        $task->save();

        return redirect()->back();
    }


      public function failedTask(Tasks $task)
    {

        $task->status = 'failed';
        $task->save();

        return redirect()->back();
    }
}
