<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    public function index()
    {
      $images = Image::where('users_id', Auth::user()->id)->simplePaginate(2);
      return view('images.index', compact('images'));
    }

   
    // Show the form for creating a new resource.
    public function create()
    {
        return view('images.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
{
    $request->validate([
        'image' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        'caption' => 'max:100',
    ]);
    if ($request->hasFile('image')) {
        $foto = $request->file('image');
        $filename = time() . '.' . $foto->getClientOriginalExtension();
        $destinationPath = 'image/';
        $foto->move($destinationPath, $filename);
        $image = new Image();
        $image->image = $filename;

    }
    $image->users_id = Auth::user()->id;
    $image->caption = $request->caption;
    $image->save();

    return redirect()->route('images.index')->with('success', 'Image successfully saved');
}

    // Remove the specified resource from storage.
    public function destroy(Image $image)
    {
        // Delete image from storage before deleting data from the database
        if ($image->image) {
            Storage::delete($image->image);
        }

        if ($image->delete()) {
            return redirect()->route('images.index')->with('success', 'Image successfully deleted!');
        }

        return redirect()->route('images.index')->with('error', 'Failed to delete image.');
    }
    public function show(Image $image)
{
    return view('images.show', compact('image'));
}

}