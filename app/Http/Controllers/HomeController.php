<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    public function dashboard()  {
        $searchTerm = request()->query('search');
        $query = Post::with('author')->orderBy('created_at', 'desc');

        if ($searchTerm) {
            $query->whereHas('author', function ($query) use ($searchTerm) {
                $query->where('username', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        $posts = $query->get();
        return view('admin.dashboard', compact('posts'));
    }
}
