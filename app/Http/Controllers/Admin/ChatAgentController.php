<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChatAgentController extends Controller
{
    public function index()
    {
        $agents = User::whereNotNull('is_live_chat_agent')->latest()->get();

        return view('admin.live-chat.agents', compact('agents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $agent = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $agent->forceFill(['is_live_chat_agent' => true])->save();

        return back()->with('success', 'Live chat agent created.');
    }

    public function toggle(User $user)
    {
        abort_unless($user->is_live_chat_agent !== null && ! $user->is_admin, 404);

        $user->forceFill(['is_live_chat_agent' => ! $user->is_live_chat_agent])->save();

        return back()->with('success', 'Agent access updated.');
    }
}
