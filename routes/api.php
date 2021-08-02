<?php

use App\Http\Controllers\Olympus\CommentController;
use App\Models\Shop\Order;
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
    $inversion = 0;
    $cantPedidos = $orders->count();
    foreach ($orders->get() as $order) {
      $totalMoney += $order->total_price + $order->tax;
      $totalProducts += $order->total_products;
      foreach ($order->products as $orderProduct) {
        $inversion += $orderProduct->product->production_price * $orderProduct->product_qty;
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
      'inversion' => $inversion,
      'earn' => $totalMoney - $inversion,
      'total_products' => $totalProducts,
      'cant_products' => $cantProduct,
      'money_product' => $moneyProduct,
      'cant_pedidos' => $cantPedidos,
    ]);
  }
});

Route::post('/comment', [CommentController::class, 'create'])->middleware('auth:sanctum');
Route::get('/comment', [CommentController::class, 'list'])->middleware(['auth:sanctum', 'ol.auth.admin']);

Route::get('/min-price', function () {
  return response()->json(['DATA' => 40, 'STATUS' => true, 'ERRORS' => null]);
});

Route::prefix('/olympus/app')->group(__DIR__ . '/api_routes/olympus_apps.php');
Route::prefix('/olympus/announcement')->group(__DIR__ . '/api_routes/olympus_announcements.php');

Route::middleware('ol.app')->group(function () {
  Route::prefix('/olympus/notification')->group(__DIR__ . '/api_routes/olympus_notifications.php');
  Route::prefix('/shop/order')->group(__DIR__ . '/api_routes/shop_orders.php');
  Route::prefix('/shop/product')->group(__DIR__ . '/api_routes/shop_products.php');
  Route::prefix('/user')->group(__DIR__ . '/api_routes/user.php');
});
