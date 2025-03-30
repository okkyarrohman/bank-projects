<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function getData()
    {
        $categories = DB::table('categories')
            ->select('id', 'name')
            ->where('status', 'Active')
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }
}
