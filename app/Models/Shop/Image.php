<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image as ImageIntervention;

class Image extends Model
{
  use HasFactory;
  protected $table = 'shop_images';
  protected $guarded = ['id'];

  protected $casts = [
    'tags' => 'array',
    'paths' => 'array'
  ];

  public $STORAGE_PATH = "/public/shop/images";
  public $PUBLIC_PATH = "/storage/shop/images";

  public function getUrlsAttribute()
  {
    $urls = [];
    foreach ($this->paths as $key => $path) {
      $urls[$key] = config('app.url') . $path;
    }
    return $urls;
  }

  /**
   * Upload images 
   * @example uploadImage($image, 'product', ['md', 'sm'])
   * @param $image
   * @param $type
   * @param $sizes
   */
  public function uploadImage($image, $type = 'product', $typeId, $sizes = ['sm', 'md', 'lg'])
  {
    // $filename =  sha1($image->getClientOriginalName()) . '_' . sha1(time()) . '.' . $image->getClientOriginalExtension();
    $filename =  sha1($image->getClientOriginalName()) . '_' . sha1(time()) . '.jpg';
    // $filename =  $type . '-' . $typeId . '.jpg';
    $storage_path = $this->STORAGE_PATH;
    $public_path = $this->PUBLIC_PATH;
    switch ($type) {
      case 'product':
        $storage_path .= '/product';
        $public_path .= '/product';
        break;
      case 'vendor':
        $storage_path .= '/vendor';
        $public_path .= '/vendor';
        break;
      case 'announcement':
        $storage_path .= '/announcement';
        $public_path .= '/announcement';
        break;
      default:
        $storage_path .= '/';
        $public_path .= '/';
    }
    $paths = [];
    $resizeDimension = 480;
    for ($i = 0; $i < count($sizes); $i++) {
      $resizeName = $filename;
      switch ($sizes[$i]) {
        case 'sm':
          $resizeName = 'sm_' . $resizeName;
          break;
        case 'md':
          $resizeName = 'md_' . $resizeName;
          $resizeDimension = 640;

          break;
        case 'lg':
          $resizeName = 'lg_' . $resizeName;
          $resizeDimension = 980;
          break;
      }
      if ($resizeName !== $filename) {
        $pathCpy = $storage_path  . '/' . $resizeName;
        $paths[$sizes[$i]] = $public_path . '/' . $resizeName;
        if(Storage::exists($pathCpy))
          Storage::delete($pathCpy);
        Storage::put($pathCpy, '');
        try {
          $imageFile = ImageIntervention::make($image)
            ->resize($resizeDimension, null, function ($constraints) {
              $constraints->aspectRatio();
            })->save(storage_path('/app' . $pathCpy));
        } catch (NotReadableException $e) {
          Storage::put('image_error.' . json_encode($e));
        }
      }
    }
    $this->paths = $paths;
    return $this;
  }
}
