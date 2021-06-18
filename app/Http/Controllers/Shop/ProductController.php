<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Olympus\App as OlympusApp;
use App\Models\Shop\Category;
use App\Models\Shop\Image;
use App\Models\Shop\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
  /**
   * Get product by id
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function cById(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $product = Product::query()->where('onsale', true)->find($validator['product_id'], Product::tableFields());

      $this->API_RESPONSE['DATA'] = $product;
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }
  /**
   * Get product by id
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vById(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $product = Product::query()->find($validator['product_id']);
      if ($product) {
        if ($product->owner_id === auth()->user()->id || auth()->user()->hasAnyRole(['Developer', 'Admin'])) {
          $this->API_RESPONSE['DATA'] = $product;
          $this->API_RESPONSE['STATUS'] = true;
        } else {
          $this->API_RESPONSE['ERRORS'] = ['No tiene suficientes privilegios'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['UNAUTHORIZED'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['Producto no encontrado'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Filter Products
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function filter(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'ol_app_token' => ['required', 'string'],
      'owner_id' => ['nullable', 'integer'],
      'title' => ['nullable', 'string', 'max:256'],
      'max_price' => ['nullable', 'numeric'],
      'min_price' => ['nullable', 'numeric'],
      'sell_price' => ['nullable', 'numeric'],
      'stock_qty' => ['nullable', 'integer'],
      'stock_status' => ['nullable', 'in:' . implode(',', Product::$STOCK_STATUS)],
      'wholesale' => ['nullable', 'boolean'],
      'wholesale_min' => ['nullable', 'integer'],
      'wholesale_price' => ['nullable', 'numeric'],
      'tags' => ['nullable', 'array'],
      'category' => ['nullable', 'string']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      // Starting Query
      $productQry = Product::query()->where('onsale', true);
      $extraModelColumns = [];
      // Add Visit to application
      $olympusApp = OlympusApp::getByToken($validator['ol_app_token']);
      $lastVisit = Carbon::createFromTimeString($olympusApp->updated_at);
      if ($lastVisit->dayOfYear < now()->dayOfYear) {
        $olympusApp->daily_visits = 0;
      }
      $olympusApp->daily_visits++;
      $olympusApp->save();
      // Owner
      if (isset($validator['owner_id']))
        $productQry = $productQry->where('owner_id', $validator['owner_id']);
      // Title
      if (isset($validator['title']))
        $productQry = $productQry->where('title', 'like', '%' . $validator['title'] . '%');
      // Max Price
      if (isset($validator['max_price']))
        $productQry = $productQry->where('sell_price', '<', $validator['max_price']);
      // Min Price
      if (isset($validator['min_price']))
        $productQry = $productQry->where('sell_price', '>', $validator['min_price']);
      // Sell Price
      if (isset($validator['sell_price']))
        $productQry = $productQry->where('sell_price', $validator['sell_price']);
      // Stock Qty
      if (isset($validator['stock_qty']))
        $productQry = $productQry->where('stock_qty', '>', $validator['stock_qty']);
      // Stock Status
      if (isset($validator['stock_status']))
        $productQry = $productQry->where('stock_status', '>', $validator['stock_status']);
      // Wholesale
      if (isset($validator['wholesale'])) {
        $productQry = $productQry->where('wholesale', $validator['wholesale']);
        array_push($extraModelColumns, 'wholesale');
        array_push($extraModelColumns, 'wholesale_min');
        array_push($extraModelColumns, 'wholesale_price');
      }
      // wholesale min
      if (isset($validator['wholesale_min']))
        $productQry = $productQry->where('wholesale_min', '>', $validator['wholesale_min']);
      // Wholesale Price
      if (isset($validator['wholesale_price']))
        $productQry = $productQry->where('wholesale_price', '<', $validator['wholesale_price']);
      // Tags
      if (isset($validator['tags']))
        $productQry = $productQry->whereJsonContains('tags', $validator['tags']);
      // Category
      if (isset($validator['category'])) {
        $category = Category::query()->where('tag', $validator['category']);
        if ($category->exists()) {
          $category = $category->first(['id']);
          $productQry = $productQry->where('category_id', $category->id);
        }
      }
      $min_price = 40;
      $extra_price = 0;
      if (isset($olympusApp->settings->min_price)) {
        $min_price = $olympusApp->settings->min_price;
      }
      if (isset($olympusApp->settings->extra_price)) {
        $extra_price = $olympusApp->settings->extra_price;
      }
      $this->API_RESPONSE['DATA'] = [
        'products' => $productQry->orderBy('updated_at', 'desc')->simplePaginate(Product::$PAGINATE, Product::tableFields($extraModelColumns)),
        'min_price' => $min_price,
        'extra_price' => $extra_price,
        'enable' => isset($olympusApp->settings->enable) ? $olympusApp->settings->enable : false
      ];
      // $this->API_RESPONSE['DATA'] = $productQry->orderBy('updated_at', 'desc')->simplePaginate(12, Product::tableFields($extraModelColumns));
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Filter Products
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function filterVendor(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'onsale' => ['nullable', 'boolean'],
      'title' => ['nullable', 'string', 'max:256'],
      'max_price' => ['nullable', 'numeric'],
      'min_price' => ['nullable', 'numeric'],
      'sell_price' => ['nullable', 'numeric'],
      'stock_qty' => ['nullable', 'integer'],
      'stock_status' => ['nullable', 'in:' . implode(',', Product::$STOCK_STATUS)],
      'wholesale' => ['nullable', 'boolean'],
      'wholesale_min' => ['nullable', 'integer'],
      'wholesale_price' => ['nullable', 'numeric'],
      'tags' => ['nullable', 'array'],
      'category' => ['nullable', 'string']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      // Starting Query
      if (auth()->user()->hasAnyRole(['Developer', 'Admin']))
        $productQry = Product::query();
      else
        $productQry = Product::query()->where('owner_id', auth()->user()->id);
      // Title
      if (isset($validator['title']))
        $productQry = $productQry->where('title', 'like', '%' . $validator['title'] . '%');
      // Max Price
      if (isset($validator['max_price']))
        $productQry = $productQry->where('sell_price', '<', $validator['max_price']);
      // Min Price
      if (isset($validator['min_price']))
        $productQry = $productQry->where('sell_price', '>', $validator['min_price']);
      // Sell Price
      if (isset($validator['sell_price']))
        $productQry = $productQry->where('sell_price', $validator['sell_price']);
      // Stock Qty
      if (isset($validator['stock_qty']))
        $productQry = $productQry->where('stock_qty', '>', $validator['stock_qty']);
      // Stock Status
      if (isset($validator['stock_status']))
        $productQry = $productQry->where('stock_status', '>', $validator['stock_status']);
      // Wholesale
      if (isset($validator['wholesale']))
        $productQry = $productQry->where('wholesale', $validator['wholesale']);
      // wholesale min
      if (isset($validator['wholesale_min']))
        $productQry = $productQry->where('wholesale_min', '>', $validator['wholesale_min']);
      // Wholesale Price
      if (isset($validator['wholesale_price']))
        $productQry = $productQry->where('wholesale_price', '<', $validator['wholesale_price']);
      // Tags
      if (isset($validator['tags']))
        $productQry = $productQry->whereJsonContains('tags', $validator['tags']);
      // Category
      if (isset($validator['category'])) {
        $category = Category::query()->where('tag', $validator['category']);
        if ($category->exists()) {
          $category = $category->first(['id']);
          $productQry = $productQry->where('category_id', $category->id);
        }
      }
      $this->API_RESPONSE['DATA'] = $productQry->simplePaginate(Product::$PAGINATE, Product::tableFields());
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Update product 
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function update(Request $request)
  {
    // Check Permission Authorization
    $user = auth()->user();
    if (!$user || !$user->can('products.update')) {
      $this->API_RESPONSE['ERRORS'] = ['No tiene suficientes permisos'];
      $this->API_STATUS = $this->AVAILABLE_STATUS['UNAUTHORIZED'];
      return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
    }
    $validator = Validator::make($request->all(), [
      'product_id' => ['required', 'integer'],
      'title' => ['nullable', 'string', 'max:256'],
      'description' => ['nullable', 'string'],
      'production_price' => ['nullable', 'numeric'],
      'regular_price' => ['regular_price', 'numeric'],
      'sell_price' => ['nullable', 'numeric'],
      'onsale' => ['nullable', 'boolean'],
      'stock_qty' => ['nullable', 'integer'],
      'stock_status' => ['nullable', 'in:' . implode(',', Product::$STOCK_STATUS)],
      'weight' => ['nullable', 'integer'],
      'dimensions' => ['nullable', 'string'],
      'tax' => ['nullable', 'numeric'],
      'wholesale' => ['nullable', 'boolean'],
      'wholesale_min' => ['nullable', 'integer'],
      'wholesale_price' => ['nullable', 'numeric'],
      'attributes' => ['nullable', 'array'],
      'tags' => ['nullable', 'array'],
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $product = Product::query()->find($validator['product_id']);
      $authorization = false;
      if ($product && ($user->id === $product->owner_id || $user->hasAnyRole(['Developer', 'Admin']))) {
        $authorization = true;
      } else if ($user->hasRole(['Admin', 'Developer'])) {
        $authorization = true;
      }
      if ($authorization) {
        if ($product) {
          // Insert not null values
          foreach ($validator as $key => $value) {
            if (isset($product[$key])) {
              $product[$key] = $value;
            }
          }
          // Insert Nullable values
          $nullableKeys = ['production_price', 'regular_price', 'dimensions', 'wholesale_min', 'wholesale_price', 'tags'];
          foreach ($nullableKeys as $key) {
            if (isset($validator[$key])) {
              $product[$key] = $validator[$key];
            }
          }
          $product['attributes'] = isset($validator['attributes']) ? $validator['attributes'] : null;
          if ($product->save()) {
            $this->API_RESPONSE['DATA'] = $product;
            $this->API_RESPONSE['STATUS'] = true;
          } else {
            $this->API_RESPONSE['ERRORS'] = $product->errors;
            $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['Producto no encontrado'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['No está autorizado'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['UNAUTHORIZED'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * List Products
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function listByVendor(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'onsale' => ['nullable', 'boolean']
    ]);
    if ($validator->fails() || !Auth::check()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      if (auth()->user()->hasAnyRole(['Developer', 'Admin']))
        $productQry = Product::query();
      else
        $productQry = Product::query()->where('owner_id', auth()->user()->id);
      if (isset($validator['onsale'])) {
        $productQry = $productQry->where('onsale', $validator['onsale']);
      }
      if (isset($validator['orderBy']))
        $productQry = $productQry->orderBy($validator['orderBy'], 'desc');
      $this->API_RESPONSE['DATA'] = $productQry->orderBy('updated_at', 'desc')->simplePaginate(Product::$PAGINATE, Product::tableFields(['sold', 'onsale']));
      $this->API_RESPONSE['STATUS'] = true;
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Remove Product
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function remove(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $product = Product::query()->find($validator['product_id']);
      if ($product) {
        // Check Authorization
        if (auth()->user()->hasRole(['Admin', 'Developer']) || auth()->user()->id === $product->owner_id) {
          if ($product->delete()) {
            $this->API_RESPONSE['STATUS'] = true;
          } else {
            $this->API_RESPONSE['ERRORS'] = $product->errors;
            $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['No está autorizado'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['UNAUTHORIZED'];
        }
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS, [], JSON_NUMERIC_CHECK);
  }

  /**
   * Upload Image
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function vUploadImage(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'product_id' => ['required', 'integer'],
      'image' => ['required', 'image'],
    ]);
    if ($validator->fails()) {
      $this->API_RESPONSE['ERRORS'] = $validator->errors();
      $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
    } else {
      $validator = $validator->validate();
      $product = Product::query()->find($validator['product_id']);
      if ($product) {
        $imageCoverFile = $validator['image'];
        $imageCoverModel = new Image();
        $imageCoverModel->uploadImage($imageCoverFile, 'product');
        $imageCoverModel->tags = $product->tags;
        $imageCoverModel->title = $product['title'] . '-cover';
        if ($imageCoverModel->save()) {
          $product->image_id = $imageCoverModel->id;
          if ($product->save()) {
            $this->API_RESPONSE['DATA'] = $product;
            $this->API_RESPONSE['STATUS'] = true;
            $this->API_STATUS = $this->AVAILABLE_STATUS['CREATED'];
          } else {
            $this->API_RESPONSE['ERRORS'] = ['Error guardando producto'];
            $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
          }
        } else {
          $this->API_RESPONSE['ERRORS'] = ['Error guardando Imagen'];
          $this->API_STATUS = $this->AVAILABLE_STATUS['SERVICE_UNAVAILABLE'];
        }
      } else {
        $this->API_RESPONSE['ERRORS'] = ['No existe el producto'];
        $this->API_STATUS = $this->AVAILABLE_STATUS['BAD_REQUEST'];
      }
    }
    return response()->json($this->API_RESPONSE, $this->API_STATUS);
  }
}
