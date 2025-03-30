<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;




class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = DB::table('projects')
            ->join('categories', 'projects.category_id', '=', 'categories.id')
            ->select(
                'projects.*',
                'categories.name as categoryName'
            )
            ->orderBy('projects.updated_at', 'desc')
            ->paginate(10);

        $formattedData = [];
        foreach ($projects as $project) {
            $images = DB::table('image_projects')
                ->where('project_id', $project->id)
                ->get()
                ->toArray();


            $formattedData[] = [
                'slug' => $project->slug,
                'name' => $project->name,
                'headline' => $project->headline,
                'description' => $project->description,
                'status' => $project->status,
                'images' => $images,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $formattedData,
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $storedProject = [
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'headline' => $request->input('headline'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        // Simpan proyek dan dapatkan ID
        $projectId = DB::table('projects')->insertGetId($storedProject);

        // Periksa apakah ada gambar
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Buka gambar dengan Intervention Image
                $compressedImage = Image::make($image)
                    ->encode('webp', 80) // Konversi ke WebP dengan kualitas 80%
                    ->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio(); // Jaga rasio aspek
                        $constraint->upsize(); // Hindari pembesaran gambar
                    });

                // Periksa ukuran dan ulangi jika masih di atas 300KB
                $quality = 80;
                while (strlen($compressedImage) > 300 * 1024 && $quality > 10) {
                    $quality -= 10;
                    $compressedImage = Image::make($image)
                        ->encode('webp', $quality)
                        ->resize(1200, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                }

                // Simpan ke storage
                $imageName = 'projects/' . uniqid() . '.webp';
                Storage::put('public/' . $imageName, $compressedImage);

                // Simpan URL ke database
                DB::table('image_projects')->insert([
                    'project_id' => $projectId,
                    'urlImage' => Storage::url($imageName)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Project and images stored successfully!',
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
