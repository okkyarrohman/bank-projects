<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;
use Spatie\Image\Enums\ImageFormat;



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
        // Simpan data proyek
        $storedProject = [
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'headline' => $request->input('headline'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        $projectId = DB::table('projects')->insertGetId($storedProject);

        // Periksa apakah ada gambar
        // Periksa apakah ada gambar yang diunggah
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Tentukan nama file dan path tujuan
                $imageName = 'projects/' . uniqid() . '.webp';
                $compressedPath = storage_path('app/public/' . $imageName);

                // Ambil konten gambar langsung dari request
                $image->storeAs('public/projects', basename($compressedPath)); // Simpan sementara

                // Proses gambar langsung dari file yang diunggah
                Image::load($image->getPathname())
                    ->format(ImageFormat::Webp)
                    ->width(1200) // Resize agar maksimal 1200px lebar
                    ->optimize()  // Kompres otomatis
                    ->save($compressedPath);

                // Simpan ke database
                DB::table('image_projects')->insert([
                    'project_id' => $projectId,
                    'urlImage' => Storage::url($imageName),
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
