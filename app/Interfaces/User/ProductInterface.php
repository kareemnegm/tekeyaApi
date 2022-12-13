<?php

namespace App\Interfaces\User;

interface ProductInterface {

    /**
     * Undocumented function
     *
     * @return void
     */
    public function mostPopularProduct($request);

    /**
     * Undocumented function
     *
     * @return void
     */
    public function productJustForYou($request);

        /**
     * Undocumented function
     *
     * @return void
     */
    public function relatedProducts($productId);

        /**
     * Undocumented function
     *
     * @return void
     */
    public function similarProducts($productId);


    public function getVariantsValues($variant_id);

}
