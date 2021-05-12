<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	use HasFactory;
	protected $table = 'shop_categories';
	protected $guarded = ['id'];

	public function parentCategory()
	{
		return $this->belongsTo(Category::class, 'parent_id', 'id');
	}
}
