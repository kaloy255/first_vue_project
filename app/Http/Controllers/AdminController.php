<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request){
        //assign to input
        $searchUser = $request->input('search');
        //admin id
        $currentUserId = $request->user()->id;

        $usersTasks = Tasks::with(['assignedUser', 'creator'])->paginate(10);
        $users = User::query()
        ->when($searchUser, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->where('id', '!=', $currentUserId)
            ->get(['id', 'name', 'email']);
        
    
        return Inertia::render('Admin/Dashboard', [
            'users' => $users,
            'usersTasks'=> $usersTasks,
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
