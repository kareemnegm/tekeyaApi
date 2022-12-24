<?php

namespace App\Interfaces;

interface CategoryInterface{

    /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function createCategory($details);
    /**
     * 
     */
    public function UpdateCategory($details,$id);
    /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function getCategories($details);
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function deleteCategory($id);
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function getCategory($id);
 /**
     * Undocumented function
     *
     * @param [type] $details
     * @return void
     */
    public function getAllCategories($details);

    
}
