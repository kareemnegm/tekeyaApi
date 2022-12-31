<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderShopDetails;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateProductsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $users=DB::connection('tekeya')->table('Users')->get();


        foreach($users as $user){

            $userData=[
                'email'=>$user->email,
                'password'=>$user->password,
                'mobile'=>isset($user->phone) ? $user->phone :null,
                // 'country_code'=>isset($user->phone) ? explode(' ', $user->phone)[0]:null,
                'gender'=>$user->genderID == 1 ?'male':'female',

            ];

            $userExists=User::where('email',$user->email)->exists();

            if(!$userExists){
                $user=User::create($userData);
            }

        }


        $categories=DB::connection('tekeya')->table('Categories')->get();

        foreach($categories  as $category){

            $categoryData=[
                'name'=>$category->categoryName,
            ];


            $category=Category::where('name',$category->categoryName)->exists();
            if(!$category){

            $category=Category::create($categoryData);
            }


        }

        $shops=DB::connection('tekeya')->table('Providers')->get();

        foreach($shops as $shop){

            $provider=[
                'email'=>$shop->email,
                'password'=>$shop->password,
                'type'=>'shop',
                'mobile'=>$shop->phone,
                'approved'=>1,
            ];


            
            $providerExists=Provider::where('email',$shop->email)->first();

            if(!$providerExists){
            $providerCreate=Provider::create($provider);

            

            $shopData=[
                'shop_name'=>$shop->name,
                'email'=>$shop->email,
                'whatsapp_number'=>$shop->phone,
                'provider_id'=>$providerCreate->id,
                'vat'=>1,
                // 'status'=>'approved',
            ];
        
            $providerCreate=ProviderShopDetails::create($shopData);


        
        $products=DB::connection('tekeya')->table('Items')->where('providerID',$shop->id)->get();
        if($products){
        

        foreach($products as $product){

            $categoryProduct=DB::connection('tekeya')->table('Categories')->where('id',$product->categoryID)->first();

            if(isset($categoryProduct)){
            $newCategory=Category::where('name',$categoryProduct->categoryName)->first();
            }else{
                $newCategory=null;
            }

            $data=[
                'name'=>$product->name,
                'description'=>$product->description,
                'price'=> $product->priceBefore != '' ? $product->priceBefore : 30,
                'offer_price'=>$product->priceAfter != '' ? $product->priceAfter : 30,
                // 'start_date'=>$product->name,
                // 'end_date'=>$product->name,
                // 'stock_quantity'=>0,
                // 'total_weight'=>$product->name,
                // 'order'=>$product->name,
                'is_published'=>1,
                'shop_id'=>$providerCreate->id,
                'to_donation'=>$product->isDonationOnly,
                'category_id'=>isset($newCategory) ? $newCategory->id:1,
                'product_images'=>[
                    $product->image,
                ]
                // 'created_at'=>$product->name,
                // 'updated_at	'=>$product->name,
                // 'admin_id	'=>$product->name,
                // 'variants'=>$product->name,

            ];

            $product=Product::create($data);
            }
        }
     }
    }
}

}
