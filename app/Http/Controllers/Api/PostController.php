<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Post;
use App\Http\Resources\PostResource;

use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function index()
    {
        //get all posts
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

}
