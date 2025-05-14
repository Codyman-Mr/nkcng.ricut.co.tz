<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class UserController extends Controller
{
    use WithPagination;
    public $search = '';
    public function index()
    {
        $users = User::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%");
            });
        })
            ->paginate(10)
            ->onEachSide(1);



        return view('users.index', ['users' => $users]);
    }

    public function show(User $user)
    {
        return view('users.show-user', compact(['user', ]));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'phone_number' => [
                    'required',
                    'unique:users,phone_number',
                    'regex:/^(?:\+2557\d{8}|\+2556\d{8}|06\d{8}|07\d{8})$/'
                ]
            ], [
                'phone_number.unique' => "The given phone number is already in use",
                'phone_number.regex' => 'Invalid phone number',
            ]);

            User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'verification_code' => 1111,
                'password' => bcrypt('12345'),
            ]);

            return response()->json(['message' => "User added successfully."], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'patch_first_name' => 'required|string',
                'patch_last_name' => 'required|string',
                'patch_phone_number' => ['required', Rule::unique('users', 'phone_number')->ignore($user->id), 'regex:/^(?:\+2557\d{8}|\+2556\d{8}|06\d{8}|07\d{8})$/'],
            ], [
                'phone_number.regex' => 'Invalid phone number',
            ]);

            $user->update([
                'first_name' => $request->patch_first_name,
                'last_name' => $request->patch_last_name,
                'phone_number' => $request->patch_phone_number,
            ]);

            return response()->json(['message' => "User updated successfully."], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->vehicles()->exists() || $user->loans()->exists()) {
                return response()->json(['message' => 'User cannot be deleted due to existing related records.'], 400);
            }

            $user->delete();
            return response()->json(['message' => "User deleted successfully."], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
