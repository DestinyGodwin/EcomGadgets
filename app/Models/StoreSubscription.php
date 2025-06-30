<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StoreSubscription extends Model
{
       use HasUuids, SoftDeletes;

       protected $fillable = ['store_id', 'amount', 'starts_at', 'status', 'plan_id', 'reference', 'ends_at', 'transaction_id'];

       protected $hidden =[
              'laravel_through_key', 'deleted_at', 'updated_at'
       ];

       public function store(){
              return $this->belongsTo(Store::class);
       }

}
