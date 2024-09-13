<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gumlet\ImageResize;
use Tinify\Tinify;
use App\Models\User;

class UserController extends Controller
{
    // Show list of users with pagination
    public function index()
    {
        $users = User::paginate(6);
        return response()->json($users);
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle the image cropping and saving as 70x70px JPG
        $image = $request->file('image');
        $imagePath = $image->getRealPath();

        // Define paths
        $imageDir = public_path('images/');
        $optimizedDir = public_path('images/optimized/');

        // Ensure the directories exist
        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0755, true);
        }
        if (!file_exists($optimizedDir)) {
            mkdir($optimizedDir, 0755, true);
        }

        // Save the image
        $imageSavePath = $imageDir.$image->hashName();

        try {
            $imageResize = new ImageResize($imagePath);
            // Crop image to 70x70px from center
            $imageResize->crop(70, 70, 'center');
            $imageResize->save($imageSavePath, IMAGETYPE_JPEG);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image resizing failed: '.$e->getMessage()], 500);
        }

        // Optimize the image using TinyPNG API
        try {
            \Tinify\setKey(config('services.tinify.key'));

            // Read the image file into a buffer
            $imageData = file_get_contents($imageSavePath);

            // Optimize the image from buffer and save to the new path
            $source = \Tinify\fromBuffer($imageData);
            $optimizedImagePath = $optimizedDir.$image->hashName();
            $optimizedImageData = $source->toBuffer();

            // Save the optimized image
            file_put_contents($optimizedImagePath, $optimizedImageData);
        } catch (\Exception $e) {
            // Handle errors with image optimization
            return response()->json(['error' => 'Image optimization failed: '.$e->getMessage()], 500);
        }

        // Store user data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'image' => $image->hashName(),
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['user' => $user ], 201);
    }


    // Show single user
    public function show($id)
    {
        return User::findOrFail($id);
    }
}
