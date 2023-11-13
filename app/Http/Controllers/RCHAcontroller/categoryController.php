<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\RCHAcontroller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function store(Request $request)
{
    
    $request->validate([
        'category_name' => 'required|max:255',
        'category_description' => 'nullable',
    ]);
    try{
    $category=Category::create($request->all());
    return response()->json($category,201);
    }catch(\Exception $e){
        Log::error($e->getMessage());
        return response()->json(['message' => 'An error occurred while creating the category.'], 500);
    }
}
public function listCategories()
{
    try {
        $categories = Category::all();
        if (!$categories){ 
            return response()->json(' Not record found in Category', 200);
        }
        log::info('categories' .$categories);
        return response()->json($categories);
        
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['message' => 'An error occurred while fetching categories.'], 500);
    }
}
public function getCategoryById($cat_id)
{
    try {
        $categories = Category::find($cat_id);
        if (!$categories) {
            return response()->json(['message' => 'Category not found.'], 404);
        }
        
        log::info('categories' .$categories);
        return response()->json($categories);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['message' => 'Category not found.'], 404);
    }
}
public function update(Request $request, $cat_id)
{
    try {
        $category = Category::find($cat_id);
        
        // Check if the category was found before updating
        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }
        
        $category->update($request->all());
        return response()->json($category, 200);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['message' => 'An error occurred while updating the category.'], 500);
    }
}

public function delete($cat_id)
{
    try {
        $category = Category::find($cat_id);
    if (!$category){ 
        return response()->json('Category not found', 200);
    }
        if($category->delete()){
            log::info('categories' .$category);
        return response()->json(['message' => 'deleted'], 204);
        }
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['message' => 'An error occurred while deleting the category.'], 500);
    }
}    
}
