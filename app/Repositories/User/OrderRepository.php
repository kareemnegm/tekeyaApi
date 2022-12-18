<?php

namespace App\Repositories\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\CartProductResource;
use App\Http\Resources\User\OrderReviewResource;
use App\Http\Resources\User\PaymentOptionResource;
use App\Http\Resources\User\PlaceOrderResource;
use App\Http\Resources\User\UserCartResource;
use App\Interfaces\User\CartInterface;
use App\Interfaces\User\OrderInterface;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\DeliveryOption;
use App\Models\Invoices;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\OrderItem;
use App\Models\OrderPickup;
use App\Models\OrderShipment;
use App\Models\OrderShop;
use App\Models\PaymentOption;
use App\Models\Product;
use App\Models\ProviderShopDetails;
use App\Models\User;
use App\Notifications\User\UserCheckoutOrder;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderRepository extends Controller implements OrderInterface
{


    public function cancelOrder($data)
    {
        $order = Order::findOrFail($data['order_id']);
       $orderShops= $order->orderShops()->whereHas('deliveryType', function ($query) {
            $query->where('order_shop_status', 'pending');
        })->get();
        foreach($orderShops as $orderShop){
            $orderShop->deliveryType()->update(['order_user_status'=>'canceled','order_shop_status'=>'canceled']);
        }

    }
    /**
     * New Shop Liste function
     *
     * @param [type] $projectId
     * @return void
     */
    public function orderReview($request)
    {
        $shops = [];

        foreach ($request['order_review']  as $review) {
            $cart_id = Auth::user()->cart->id;

            $shop = ProviderShopDetails::whereHas(
                'cart',
                function ($query) use ($cart_id, $review) {
                    $query->where('cart_id', $cart_id)->where('provider_shop_details_id', $review['shop_id']);
                }
            )->first();

            $shop['delivery_option_id'] = $review['delivery_option_id'];

            if (isset($review['branch_id'])) {
                $shop['branch_id'] = $review['branch_id'];
            } elseif (isset($review['address_id'])) {
                $shop['address_id'] = $review['address_id'];
            }
            array_push($shops, $shop);
        }

        $orderResource = OrderReviewResource::collection($shops);
        $orderReview = collect($orderResource);



        $payment = PaymentOption::FindOrFail($request['payment_id']);





        return [
            'total_products_price' => $orderReview->sum('total_price'),
            'total_taxes' => $orderReview->sum('shop_taxes'),
            'total_shipping_fees' => $orderReview->sum('shop_shipping_fees'),
            'total_shops' => $orderReview->count(),
            'payment_option' => $orderReview->count(),

            'total_products' => $orderReview->sum(function ($value) {
                return count($value['products']);
            }),

            'payment_option' => new PaymentOptionResource($payment),

            'order_review' => $orderResource


        ];


        return $shops;
    }


    /**
     * Place Order function
     *
     * @param [type] $projectId
     * @return object
     */
    public function placeOrder($req)
    {

        $user = Auth::user();

        $userCart = CartProduct::with(['shop', 'product'])->where('cart_id', $user->cart->id)->get();

        if ($this->productsAreNoLongerAvailable($userCart)) {
            return $this->errorResponseWithMessage('Sorry! One of the items in your cart is no longer avialble.', 422);
        }


        $date = Carbon::now();



        $total_items = $userCart->count();
        $total_shop = $userCart->unique('provider_shop_details_id')->count();


        DB::beginTransaction();
        try {

            $orderData = [
                'date_order_placed' => $date,
                'user_id' => $user->id,
                'payment_id' => $req['payment_id'],
                'total_items' => $total_items,
                'total_shop' => $total_shop,
            ];

            $order = $this->mainOrderDetails($orderData);

            /**
             *
             */

            $totalShopItemPrice = 0;
            $taxes = 0;
            $totalShipping = 0;

            foreach ($req['shops'] as $shopItem) {

                $shopItems = CartProduct::with(['shop', 'product'])->where('cart_id', $user->cart->id)->where('provider_shop_details_id', $shopItem['id'])->get();


                $shopItemsPrice = $shopItems->sum(function ($product) {
                    return $product->product->order_price * $product->quantity;
                });

                $totalShopItemPrice += $shopItemsPrice;

                if ($shopItems->first()->shop->vat == 0) {
                    $shopTaxes = round(($shopItemsPrice * 14 / 100), 2);
                    $taxes += $shopTaxes;
                }


                $shopShpping = 30;
                $totalShipping += $shopShpping;

                $orderShopInvoice = [
                    'coupon_id' => null,
                    'shipment_fees' => 30,
                    'discount' => 0,
                    'total_product_price' => $shopItemsPrice,
                    'total_invoice' => $shopItemsPrice + $shopShpping - 0,
                    'invoice_date' => $date,
                    "taxes" => $shopTaxes,
                    'user_id' => $user->id,

                ];

                $shopInvoice = $this->shopsOrderInvoice($orderShopInvoice);

                $orderShop = [
                    'order_id' => $order->id,
                    'shop_id' => $shopItem['id'],
                    'invoice_id' => $shopInvoice->id,
                    'delivery_option_id' => $shopItem['delivery_option_id'],
                    'total_items' => $shopItems->count(),
                    'note' => null,
                    'user_id' => $user->id,
                ];



                $shopOrder = $this->shopOrder(
                    $orderShop,
                    $shopItem['delivery_option_id'],
                    isset($shopItem['address_id']) ? $shopItem['address_id'] : null,
                    isset($shopItem['branch_id']) ? $shopItem['branch_id'] : null
                );


                foreach ($shopItems as $shopProduct) {
                    $product = [
                        'order_shop_id' => $shopOrder->id,
                        'order_id' => $order->id,
                        'product_id' => $shopProduct->product->id,
                        'quantity' => $shopProduct->quantity,
                        'unit_price' => $shopProduct->product->order_price,
                        'unit_total' => $shopProduct->product->order_price * $shopProduct->quantity,
                    ];

                    OrderItem::create($product);
                }
            }

            $orderInvoice = [
                'total_product_price' => $totalShopItemPrice,
                'tekya_wallet' => null,
                'tekya_points' => null,
                'user_id' => $user->id,
                'shipping_fees' => $totalShipping,
                'taxes' => $taxes,
                'grand_total_price' => $totalShopItemPrice + $taxes + $totalShipping,
            ];


            if ($totalShopItemPrice + $taxes + $totalShipping != $req['grand_total_price']) {


                // return $this->errorResponseWithMessage('Your grand total change to another value,check items price',422);
            }


            $orderInvoice = $this->mainOrderInvoice($orderInvoice);

            $order->update(['order_invoice_id' => $orderInvoice->id]);

            //  Notification::sendNow(null, new UserCheckoutOrder('Test Fire Base','Test Fire Base Order',User::where('id',19)->firstOrfail()->fcm_token));

            DB::commit();

            // $userCart = CartProduct::with(['shop', 'product'])->where('cart_id', $user->cart->id)->delete();

            return $this->dataResponse(
                new PlaceOrderResource($order),
                'Order Checkout Successfully',
                200
            );
        } catch (HttpResponseException $exception) {
            DB::rollback();
            return $exception->getResponse()->getData();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponseWithMessage('Order not place please try agin', 422);
        }
    }


    /**
     * Main Order Created function
     *
     * @param [type] $orderData
     * @return object
     */
    private function mainOrderDetails($orderData)
    {
        $orderCount = Order::count();
        $orderData['order_number'] = '#' . str_pad($orderCount + 1, 8, "0", STR_PAD_LEFT);

        $createOrder = Order::create($orderData);
        return $createOrder;
    }


    /**
     * Main Order Created function
     *
     * @param [type] $orderData
     * @return object
     */
    private function mainOrderInvoice($orderInvoice)
    {

        // $latestOrderInvoiceCount = OrderInvoice::count();
        // $orderInvoice['order_invoice_number'] = '#'.str_pad($latestOrderInvoiceCount+1, 8, "0", STR_PAD_LEFT);
        $orderInvoice = OrderInvoice::create($orderInvoice);
        return $orderInvoice;
    }


    /**
     * Undocumented function
     *
     * @param [type] $orderId
     * @return object
     */
    private function shopOrder($orderShop, $deliveryOption, $address_id = null, $branch_id = null)
    {




        $deliveryOption = DeliveryOption::findOrFail($deliveryOption);


        if ($deliveryOption->shipment_type  == 'address') {
            // dd($address_id,$deliveryOption->shipment_type );
            $optionData = [
                'address_id' => $address_id,
            ];
            $option = OrderShipment::create($optionData);
        } elseif ($deliveryOption->shipment_type  == 'branch') {
            $optionData = [
                'branch_id' => $branch_id,
            ];
            $option  = OrderPickup::create($optionData);
        }

        $shopOrder = $option->deliveryOptions()->create($orderShop);


        return $shopOrder;
    }
    /**
     * Undocumented function
     *
     * @param [type] $orderId
     * @return object
     */
    private function shopsOrderInvoice($shopOrderInvoice)
    {

        $invoiceCount = Invoices::count();
        $shopOrderInvoice['shop_invoice_number'] = '#' . str_pad($invoiceCount + 1, 8, "0", STR_PAD_LEFT);


        $shopInvoice = Invoices::create($shopOrderInvoice);
        return $shopInvoice;
    }
    /**
     * Check If Products Aviliable With Stock function
     *
     * @return object
     */
    protected function productsAreNoLongerAvailable($products)
    {

        foreach ($products as $item) {
            $product = Product::find($item->product_id);
            if ($product->stock_quantity < $item->quantity) {
                return true;
            }
        }

        return false;
    }


    /**
     * Check If Products Aviliable With Stock function
     *
     * @return void
     */
    public function myOrderList($request)
    {
        $user = Auth::user();

        $limit = $request->limit ? $request->limit : 10;

        $q = Order::where('user_id', $user->id)->withSum('orderItems', 'quantity');
        // if ($request->page) {
        //     $orders = $q->orderBy('date_order_placed', 'DESC')->paginate($limit);
        // } else {
        $orders = $q->orderBy('date_order_placed', 'DESC')->get();
        // }

        return $orders;
    }

    /**
     * Check If Products Aviliable With Stock function
     *
     * @return void
     */
    public function orderDetails($request)
    {
        $user = Auth::user();

        $order = Order::where('user_id', $user->id)->where('order_number', $request['order_number'])->firstOrFail();

        return $order;
    }
}
