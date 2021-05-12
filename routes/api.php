<?php

use App\Models\Shop\Image;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::get('/stats', function (Request $request) {
  $validator = Validator::make(
    $request->all(),
    [
      'month' => ['nullable', 'integer'],
      'day' => ['nullable', 'integer'],
      'date_since' => ['nullable', 'date'],
      'date_until' => ['nullable', 'date'],
      'date' => ['nullable', 'date']
    ]
  );
  if ($validator->fails()) {
    return response()->json($validator->errors, 404, [JSON_NUMERIC_CHECK]);
  } else {
    $validator = $validator->validate();
    $orders = Order::query()->where('status', 'completed')->with('products');
    if (isset($validator['date'])) {
      $orders = $orders->whereDate('created_at', $validator['date']);
    } else {
      if (isset($validator['date_since']) && isset($validator['date_until'])) {
        $orders = $orders->whereDate('created_at', '>=', $validator['date_since'])->whereDate('created_at', '<=', $validator['date_until']);
      } else {
        if (isset($validator['month']))
          $orders = $orders->whereMonth('created_at', $validator['month']);
        if (isset($validator['day']))
          $orders = $orders->whereDay('created_at', $validator['day']);
      }
    }
    $totalProducts = 0;
    $totalMoney = 0;
    $cantProduct = [];
    $moneyProduct = [];
    $cantPedidos = $orders->count();
    foreach ($orders->get() as $order) {
      $totalProducts += $order->total_products;
      foreach ($order->products as $orderProduct) {
        $totalMoney += $orderProduct->product_qty * $orderProduct->product->sell_price;
        if (!isset($cantProduct[$orderProduct->product->title]))
          $cantProduct[$orderProduct->product->title] = 0;
        $cantProduct[$orderProduct->product->title] += $orderProduct->product_qty;
        if (!isset($moneyProduct[$orderProduct->product->title]))
          $moneyProduct[$orderProduct->product->title] = 0;
        $moneyProduct[$orderProduct->product->title] += $orderProduct->product_qty * $orderProduct->product->sell_price;
      }
    }
    return response()->json([
      'total_money' => $totalMoney,
      'total_products' => $totalProducts,
      'cant_products' => $cantProduct,
      'money_product' => $moneyProduct,
      'cant_pedidos' => $cantPedidos
    ]);
  }
});

Route::get('/test', function () {
  $response = [];
  // $image = Image::query()->find(9);
  // $image->title = 'Montecristo-cover';
  // $image->paths = [
  //   "sm" => "/storage/shop/images/product/sm_9.jpg",
  //   "md" => "/storage/shop/images/product/md_9.jpg",
  //   "lg" => "/storage/shop/images/product/lg_9.jpg",
  // ];
  // if ($image->save()) {
  //   $response['image'] = $image;
  //   $product = new Product([
  //     'title' => 'Montecristo',
  //     'description' => 'Montecristo',
  //     'sell_price' => 0,
  //     'category_id' => 2,
  //     'owner_id' => 1,
  //     'image_id' => $image->id,
  //   ]);
  //   if ($product->save()) {
  //     $response['product'] = $product;
  //   } else {
  //     $response['error_product'] = $product->errors;
  //   }
  // } else {
  //   $response['error_image'] = $image->errors;
  // }
  // $product = Product::query()->find(8);
  // $product->image_id = 10;
  // if ($product->save())
  //   $response = $product;
  // else
  //   $response = $product->errors;

  return response()->json($response);
});

Route::get('/min-price', function () {
  return response()->json(['DATA' => 40, 'STATUS' => true, 'ERRORS' => null]);
});

Route::prefix('/olympus/app')->group(__DIR__ . '/api_routes/olympus_apps.php');

Route::middleware('ol.app')->group(function () {
  Route::prefix('/olympus/notification')->group(__DIR__ . '/api_routes/olympus_notifications.php');
  Route::prefix('/shop/order')->group(__DIR__ . '/api_routes/shop_orders.php');
  Route::prefix('/shop/product')->group(__DIR__ . '/api_routes/shop_products.php');
  Route::prefix('/user')->group(__DIR__ . '/api_routes/user.php');
});
