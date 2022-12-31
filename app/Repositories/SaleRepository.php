<?php

namespace App\Repositories;

use App\Interfaces\SaleInterface;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class SaleRepository implements SaleInterface
{
    public function createSale($details)
    {
        $categories = $details['category_id'];
        foreach ($categories as $category) {
            $details['category_id'] = $category;
            $date = date('Y-m-d');
            $checkIfSaleExists = Sale::where('category_id', $details['category_id'])->where('shop_id', $details['shop_id'])->where('end_date', '>=', $date)->value('id');
            if (isset($checkIfSaleExists)) {
                $updateSale = Sale::find($checkIfSaleExists);
                $updateSale->update($details);
            } else {
                Sale::create($details);
            }
        }
        return;
    }

    public function updateSale($details)
    {
        $sale = Sale::findOrFail($details['sale_id']);
        $sale->update($details);
        return $sale;
    }

    public function shopSales($shop_id)
    {
        $sales = Sale::where('shop_id', $shop_id)->paginate();
        return $sales;
    }


    public function deleteSales($sale_id)
    {
        $shop_id = Auth::user()->providerShopDetails->id;
        $sale = Sale::findOrFail($sale_id);
        if ($sale->shop_id == $shop_id) {
            $sale->delete();
            return true;
        } else {
            return false;
        }
    }



    public function singleSale($sale_id)
    {
        return Sale::findOrFail($sale_id);
    }
}
