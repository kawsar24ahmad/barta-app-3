<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::where('author_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        
        return view('admin.profile', compact('posts'));
    }

    
    public function edit()
    {
        return view('admin.edit-profile');
    }

    public function update(Request $request)
    {
        $userId = auth()->user()->id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|min:8',  
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',  
        ]);

        $updateData = [
            'name'=> $request->name,
            'email'=> $request->email,
            'bio'=> $request->bio,
        ];
        if ($request->hasFile('avatar')) {
            if (auth()->user()->avatar) {
                Storage::disk('public')->delete(auth()->user()->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = $avatarPath; 
        }
        
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        $updatedUser = User::where('id', $userId)
        ->update($updateData);
        if ($updatedUser) {
            return to_route('profile.index')->with('success', 'Update is Successful!');
        }
        return back()->with('error', 'Update is not Successful!');
        
    }

    
}
