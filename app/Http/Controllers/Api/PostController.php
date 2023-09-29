<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Post;
use App\Http\Resources\PostResource;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function index() {
        //tampilkan semua postingan
        $posts = Post::latest()->paginate(5);
        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request) {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');

        //check if image exists
        if ($image) {
            //upload image
            $image->storeAs('public/posts', $image->hashName());
        }

        //create post
        $post = Post::create([
            'image'     => $image ? $image->hashName() : null,
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function show($id)
    {
        //find post by ID
        $post = Post::find($id);
        //return single post as a resource
        return new PostResource(true, 'Detail Data Post!', $post);
    }

    public function update(Request $request, $id) {
        // validate request
        $request->validate([
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // find post
        $post = Post::Find($id);

        // handle image upload
        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image->storeAs('public/posts', $image->hashName());

                // delete old image if it exists
                if (Storage::exists('public/posts/' . $post->image)) {
                    Storage::delete('public/posts/' . $post->image);
                }

                // update post with new image
                $post->image = $image->hashName();
            } else {
                // set image to null if no image is uploaded
                $post->image = null;
            }

            // update post without image
            $post->title = $request->input('title');
            $post->content = $request->input('content');

            // save post
            $post->save();

            // return response
            return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        //find post by ID
        $post = Post::find($id);
        //delete image
        Storage::delete('public/posts/'.basename($post->image));
        //delete post
        $post->delete();
        //return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
