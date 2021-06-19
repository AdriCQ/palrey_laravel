<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Olympus\App as OlympusApp;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\User;
use App\Notifications\Shop\Order as ShopOrderNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

  /**
   * Customer orders
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function cList(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'status' => ['nullable', 'in:' . implode(',', Order::$STATUS)]
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $user = auth()->user();
      $orders = Order::query()->where('customer_id', $user->id);
      if (isset($validator['status'])) {
        $orders = $orders->where('status', $validator['status']);
      }
      $this->API_RESPONSE['DATA'] = $orders->orderBy('updated_at', 'desc')->simplePaginate(24);
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Count orders
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function cCount(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'status' => ['nullable', 'in:' . implode(',', Order::$STATUS)]
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $orders = Order::query()->where('customer_id', auth()->user()->id);
      if (isset($validator['status'])) {
        $orders = $orders->where('status', $validator['status']);
      }
      $this->API_RESPONSE['DATA'] = $orders->count();
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * store
   * @param Request $request
   * @return Illuminate\Http\JsonResponse
   */
  public function cStore(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'shipping_address' => ['required', 'string'],
      'coordinates' => ['nullable', 'array'],
      'coordinates.lat' => ['sometimes', 'numeric'],
      'coordinates.lng' => ['sometimes', 'numeric'],
      'message' => ['nullable', 'string'],
      'products' => ['required', 'array'],
      'products.*.id' => ['required', 'integer'],
      'products.*.qty' => ['required', 'integer'],
      'products.*.product_details' => ['nullable', 'array'],
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $olympusApp = OlympusApp::getByToken($validator['ol_app_token']);
      $user = auth()->user();
      $productQty = 0;
      $totalTax = 0;
      if (isset($olympusApp->settings->extra_price))
        $totalTax = $olympusApp->settings->extra_price;
      $totalPrice = 0;
      $orderProducts = [];
      foreach ($validator['products'] as $product) {
        $qry = Product::query()->where('onsale', true)->find($product['id']);
        // Check Product stock quantity
        if ($qry && $qry->stock_status !== 'sold_out') {
          if ($qry->stock_status === 'limited') {
            if ($qry->stock_qty >= $product['qty']) {
              $qry->stock_qty -= $product['qty'];
              // product SOLD_OUT
              if ($qry->stock_qty == 0)
                $qry->stock_status = 'sold_out';
            } else {
              $this->API_RESPONSE['ERRORS'] = ['No tenemos suficiente ' . $qry->title];
              return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
            }
          }
          $qry->sold += $product['qty'];
          // Update Product info
          if ($qry->save()) {
            $productQty += $product['qty'];
            $totalTax += $qry->tax * $product['qty'];
            $totalPrice += $qry->sell_price * $product['qty'];
            $details = null;
            if (isset($product['product_details'])) {
              $details = $product['product_details'];
            }
            array_push($orderProducts, [
              'product_id' => $qry->id,
              'product_qty' => $product['qty'],
              'product_details' => $details
            ]);
          } else {
            $this->API_RESPONSE['ERRORS'] = $qry->errors;
            $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
            return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['Producto no encontrado'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
          return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
        }
      }
      $orderData = [
        'app_id' => $olympusApp->id,
        'customer_id' => $user->id,
        'tax' => $totalTax,
        'total_price' => $totalPrice,
        'shipping_address' => $validator['shipping_address'],
        'message' => $validator['message'],
        // 'coordinates' => $validator['coordinates'],
        'total_products' => $productQty,
      ];
      if (isset($validator['coordinates'])) {
        $orderData['coordinates'] = $validator['coordinates'];
      }
      $order = new Order($orderData);
      if ($order->save()) {
        $order->products()->createMany($orderProducts);
        $this->API_RESPONSE['STATUS'] = true;
        $this->API_RESPONSE['DATA']['order'] = $order;
        // if (isset($apiToken)) {
        //   $this->API_RESPONSE['DATA']['api_token'] = $apiToken;
        //   $this->API_RESPONSE['DATA']['profile'] = $user;
        // }
        $this->API_RESPONSE['STATUS'] = true;
        $this->API_STATUS = $this->AVAILABLE_STATUS['CREATED'];
        // Send Notification
        $usersNotifiable = User::first();
        Notification::send($usersNotifiable, new ShopOrderNotification($order));
        // Notification::send($usersNotifiable, new NewOrderNotification($order));
      } else {
        $this->API_RESPONSE['ERRORS'] = $order->errors;
        $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  // TODO: Check wholesale store

  /**
   * Client wholesale shop
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function cWholesaleStore(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'shipping_address' => ['required', 'string'],
      'coordinates' => ['nullable', 'array'],
      'coordinates.lat' => ['required', 'numeric'],
      'coordinates.lng' => ['required', 'numeric'],
      'message' => ['nullable', 'string'],
      'products' => ['required', 'array'],
      'products.*.id' => ['required', 'integer'],
      'products.*.qty' => ['required', 'integer'],
      'products.*.product_details' => ['nullable', 'array'],
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $olympusApp = OlympusApp::getByToken($validator['ol_app_token']);
      $user = auth()->user();

      $productQty = 0;
      $totalTax = 0;
      $totalPrice = 0;
      $orderProducts = [];
      foreach ($validator['products'] as $product) {
        $qry = Product::query()->where([['onsale', true], ['wholesale', true]])->find($product['id']);
        // Check Product stock quantity
        if ($qry && $qry->stock_status != 'sold_out') {
          // Check Product stock status
          if ($qry->stock_status === 'limited') {
            if ($qry->stock_qty >= $product['qty']) {
              $qry->stock_qty -= $product['qty'];
              // product SOLD_OUT
              if ($qry->stock_qty == 0)
                $qry->stock_status = 'sold_out';
              else {
                $this->API_RESPONSE['ERRORS'] = ['No tenemos suficiente ' . $qry->title];
                return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
              }
            } else {
              $this->API_RESPONSE['ERRORS'] = ['No tenemos suficiente ' . $qry->title];
              return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
            }
            // Check Product wholesale
            if ($qry->wholesale_min > $product['qty']) {
              $this->API_RESPONSE['ERRORS'] = ['Debe comprar más productos'];
              $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
              return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
            }
            $qry->sold += $product['qty'];
            // Update Product info
            if ($qry->save()) {
              $productQty += $product['qty'];
              $totalTax += $qry->tax * $product['qty'];
              $totalPrice += $qry->wholesale_price * $product['qty'];
              if (isset($product['product_details'])) {
                $details = json_encode($product['product_details']);
              }
              array_push($orderProducts, [
                'product_id' => $qry->id,
                'product_qty' => $product['qty'],
                'product_details' => $details
              ]);
            } else {
              $this->API_RESPONSE['ERRORS'] = $qry->errors;
              $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
              return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
            }
          } else {
            $this->API_RESPONSE['ERRORS'] = ['No existe el Producto'];
            $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
            return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['Producto no encontrado'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
          return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
        }
        $orderData = [
          'app_id' => $olympusApp->id,
          'customer_id' => $user->id,
          'tax' => $totalTax,
          'total_price' => $totalPrice,
          'shipping_address' => $validator['shipping_address'],
          'message' => $validator['message'],
          // 'coordinates' => $validator['coordinates'],
          'total_products' => $productQty,
        ];
        if (isset($validator['coordinates'])) {
          $orderData['coordinates'] = $validator['coordinates'];
        }
        $order = new Order($orderData);
        if ($order->save()) {
          $order->products()->createMany($orderProducts);
          $this->API_RESPONSE['STATUS'] = true;
          $this->API_RESPONSE['DATA']['order'] = $order;
          // if (isset($apiToken)) {
          //   $this->API_RESPONSE['DATA']['api_token'] = $apiToken;
          //   $this->API_RESPONSE['DATA']['profile'] = $user;
          // }
          $this->API_RESPONSE['STATUS'] = true;
          $this->API_STATUS = $this->AVAILABLE_STATUS['CREATED'];
          // Send Notification
          // $usersNotifiable = User::first();
          // Notification::send($usersNotifiable, new NewOrderNotification($order));
        } else {
          $this->API_RESPONSE['ERRORS'] = $order->errors;
          $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
        }
      }
      return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
    }
  }

  /**
   * Check Order
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function checkOrder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'products' => ['required', 'array'],
      'products.*.id' => ['required', 'integer'],
      'products.*.qty' => ['required', 'integer'],
      // 'products.*.product_details' => ['nullable', 'array'],
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $this->API_RESPONSE['STATUS'] = true;
      $ERRORS = [];
      foreach ($validator['products'] as $product) {
        $qry = Product::query()->where('onsale', true)->find($product['id']);
        // Check Product stock quantity
        if ($qry) {
          // Check Product stock status
          if ($qry->stock_status === 'limited' && $qry->stock_qty < $product['qty']) {
            $this->API_RESPONSE['STATUS'] = false;
            array_push($ERRORS, 'Solamente tenemos ' . $qry->stock_qty . ' ' . $qry->title);
          } else if ($qry->stock_status === 'sold_out') {
            $this->API_RESPONSE['STATUS'] = false;
            array_push($ERRORS, 'Producto ' . $qry->title . ' AGOTADO');
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['No existe el Producto'];
          $this->API_RESPONSE['STATUS'] = false;
          $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
          return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
        }
      }
      $this->API_RESPONSE['ERRORS'] = $ERRORS;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Cancel customer order
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function cCancel(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'order_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $order = Order::query()->find($validator['order_id']);
      if ($order && $order->status !== 'c-canceled') {
        if (abs(auth()->user()->id) === abs($order->customer_id)) {
          $order->status = 'c-canceled';
          if ($order->resetProductsQty() && $order->save()) {
            $this->API_RESPONSE['STATUS'] = true;
            $this->API_RESPONSE['DATA'] = $order;
            // Send Notification
            $usersNotifiable = User::first();
            // Notification::send($usersNotifiable, new CancelOrderNotification());
          } else {
            $this->API_RESPONSE['ERRORS'] = ['Error al guardar orden'];
            $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['No tiene autorización'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['UNAUTHORIZED'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['No existe la orden'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * List orders
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vList(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'status' => ['nullable', 'string', 'in:' . implode(',', Order::$STATUS)]
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $qry = Order::query();
      if (isset($validator['status'])) {
        $qry = $qry->where('status', $validator['status']);
      }
      $qry = $qry->with(
        [
          'customer',
          'products.product'
        ]
      );
      $qry = $qry->orderBy('updated_at', 'desc');
      $this->API_RESPONSE['DATA'] = $qry->simplePaginate(24);
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Count vendor orders
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vCount(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'status' => ['nullable', 'in:' . implode(',', Order::$STATUS)]
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $orders = Order::query();
      if (isset($validator['status'])) {
        $orders = $orders->where('status', $validator['status']);
      }
      $this->API_RESPONSE['DATA'] = $orders->count();
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * changeStatus
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vChangeStatus(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'order_id' => ['required', 'integer'],
      'status' => ['required', 'string', 'in:' . implode(',', Order::$STATUS)],
      'delivery_time' => ['nullable', 'date']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $order = Order::query()->find($validator['order_id']);
      if ($order) {
        if ($validator['status'] === 'accepted') {
          if (isset($validator['delivery_time'])) {
            // TODO: Check TIme
            $order->delivery_time = Carbon::createFromDate($validator['delivery_time']);
            // $order->delivery_time = Carbon::createFromDate($validator['delivery_time'])->subHours(4);
          } else {
            $order->delivery_time = now()->addHour();
          }
        }
        if ($validator['status'] === 'v-canceled') {
          $order->resetProductsQty();
        }

        $order->status = $validator['status'];

        if ($order->save()) {
          $this->API_RESPONSE['STATUS'] = true;
          $this->API_RESPONSE['DATA'] = $order;
        } else {
          $this->API_RESPONSE['ERRORS'] = $order->errors;
          $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['No se encontró la orden'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Delete
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vDelete(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'order_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $order = Order::query()->find($validator['order_id']);
      if ($order) {
        if ($order->delete()) {
          $this->API_RESPONSE['STATUS'] = true;
        } else {
          $this->API_RESPONSE['ERRORS'] = $order->errors;
          $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['Orden no encontrada'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
}
