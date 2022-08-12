<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class UserController extends Controller
{
    use PasswordValidationRules;

    private function data()
    {
        $items = User::query();
        $items->when(request('s'), function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%'.$search.'%')
                      ->orWhere('name', 'like', '%'.$search.'%')
                      ->orWhere('email', 'like', '%'.$search.'%');
            });
        });
        $items->when(request('id'), function ($query, $value) {
            $query->whereId($value);
        });
        $items->when(request('name'), function ($query, $value) {
            $query->where('name', 'like', '%'.$value.'%');
        });
        $items->when(request('email'), function ($query, $value) {
            $query->where('name', 'like', '%'.$value.'%');
        });
        $items->orderBy(request('col', 'id'), request('dir', 'desc'));

        return $items->paginate(10, ['id', 'name', 'email'])
                     ->withQueryString();
    }

    public function index()
    {
        $items = $this->data();

        return Inertia::render('Users/Index', [
            'items' => $items,
            'f'     => request()->all(['s', 'id', 'name', 'email', 'col', 'dir']),
        ]);
    }

    public function show(User $user)
    {
        return $user->only(['id', 'name', 'email']);
    }

    public function store()
    {
        Validator::make(request()->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        User::create([
            'name'     => request('name'),
            'email'    => request('email'),
            'password' => Hash::make(request('password')),
        ]);

        return redirect()->back()->banner('User created!');
    }

    public function update(User $user)
    {
        Validator::make(request()->all(), [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users, email,'.$user->id],
        ])->validate();

        $user->update([
            'name'  => request('name'),
            'email' => request('email'),
        ]);

        return redirect()->back()->banner('User updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->banner('User deleted!');
    }
}
