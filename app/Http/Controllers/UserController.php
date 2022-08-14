<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function data()
    {
        $items = User::query();
        $items->when(request('search'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%'.$search.'%')
                      ->orWhere('name', 'like', '%'.$search.'%')
                      ->orWhere('email', 'like', '%'.$search.'%');
            });
        });

        $items->when(request('filters.id'), function ($query, $value) {
            $query->whereId($value);
        });
        $items->when(request('filters.name'), function ($query, $value) {
            $query->where('name', 'like', '%'.$value.'%');
        });
        $items->when(request('filters.email'), function ($query, $value) {
            $query->where('email', 'like', '%'.$value.'%');
        });

        $items->orderBy(request('sortBy', 'id'), request('sortDir', 'desc'));

        return $items->paginate(10, ['id', 'name', 'email']);
    }

    public function index()
    {
        return Inertia::render('Users/Index');
    }

    public function show(User $user)
    {
        return $user->only(['id', 'name', 'email']);
    }

    public function store()
    {
        Validator::make(request()->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email'),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        User::create([
            'name'     => request('name'),
            'email'    => request('email'),
            'password' => Hash::make(request('password')),
        ]);

        return redirect()->route('users.index')->banner('User created!');
    }

    public function update(User $user)
    {
        Validator::make(request()->all(), [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id),
            ],
        ])->validate();

        $user->update([
            'name'  => request('name'),
            'email' => request('email'),
        ]);

        return redirect()->route('users.index')->banner('User updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->banner('User deleted!');
    }
}
