<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveStoreScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
      public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('store', function ($query) {
            $query->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('subscription_expires_at') 
                        ->orWhere('subscription_expires_at', '>=', now());
                  });
        });
    }
}
