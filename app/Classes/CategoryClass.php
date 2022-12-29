<?php

namespace App\Classes;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\System\AllCategoryResource;
use App\Interfaces\CategoryInterface;
use App\Models\Category;

class CategoryClass implements CategoryInterface
{
     /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function createCategory($details)
    {
        $category=Category::create($details);

        if (isset($details['category_icon'])) {
            $category->saveFiles($details['category_icon'], 'category_icon');
        }

        return $category;
    }


    /**
     * Undocumented function
     *
     * @param [type] $details
     * @param [type] $id
     * @return void
     */
    public function updateCategory($details, $id)
    {
        $category = Category::findOrFail($id);

        $category->update($details);

        if (isset($details['category_icon'])) {

            if ($category->getMedia('category_icon')) {

                $category->clearMediaCollectionExcept(' ');
            }

            $category->saveFiles($details['category_icon'], 'category_icon');
        }
    
        return $category;
    }

    /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function getCategories($details)
    {
        $q=Category::query();

       if($details['keyword']){
       $categoies=$q->where('name', 'LIKE', '%' . $details['keyword'] . '%')->with('children')->get();
       }else{
        $categoies=$q->where('category_id', null)->with('children')->get();
       }

        return CategoryResource::collection($categoies);
    }

    /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function getAllCategories($details)
    {
        
        $q=Category::query();

       if($details['keyword']){
       $categoies=$q->where('name', 'LIKE', '%' . $details['keyword'] . '%')->get();
       }else{
        $categoies=$q->get();
       }

        return AllCategoryResource::collection($categoies);
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function getCategory($id)
    {
        return  new CategoryResource(Category::findOrFail($id));
    }

}
