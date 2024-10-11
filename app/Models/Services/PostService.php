<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService extends Model
{
    use HasFactory;

    public function validatePost(Request $request): array
    {
        $validated = $request->validate([
            'barta' => 'nullable|string|max:255|required_without:picture',
            'picture' => 'nullable|image|max:2048|required_without:barta',
        ]);

        return $validated;
    }

    public function search() {
        
    }


    public function getAllPostsWithAuthors()
    {
        $searchTerm = request()->query('search');
        $query = Post::with('author')->orderBy('created_at', 'desc');

        if ($searchTerm) {
            $query->whereHas('author', function ($query) use ($searchTerm) {
                $query->where('username', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        return $query->get();
    }

    public function storePost(Request $request)
    {
        $validated = $this->validatePost($request);
        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('pictures', 'public');
        }



        

        return  Post::create([
            'body' => $validated['barta'] ?? "",
            'slug' => Str::uuid(),
            'author_id' => auth()->id(),
            'picture' => $picturePath,
        ]);     
    }

    public function updatePost(Request $request,  $id)  {
        $post = Post::with('author')->find($id);
        $validated = $this->validatePost($request);
   
        
        if (isset($validated['barta'])) {
            $post->body = $validated['barta'];
        }
        if ($request->hasFile('picture')) {
            if ($post->picture) {
                Storage::disk('public')->delete($post->picture);
            }
            $avatarPath = $request->file('picture')->store('pictures', 'public');
            $post->picture = $avatarPath;
        }
  
        return $post->save();
    }
}
