<?php

namespace App\Models\Scopes;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class ActiveStoreScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    //   public function apply(Builder $builder, Model $model): void
    // {
    //     $builder->whereHas('store', function ($query) {
    //         $query->where('is_active', true)
    //               ->where(function ($q) {
    //                   $q->whereNull('subscription_expires_at') 
    //                     ->orWhere('subscription_expires_at', '>=', now());
    //               });
    //     });
    // }

     public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();
        if ($user && $user->role === 'admin') {
            return;
        }
        $builder->whereHas('store', function ($query) use ($user) {
            $query->where(function ($q) {
                $q->where('is_active', true)
                  ->where(function ($subQ) {
                      $subQ->whereNull('subscription_expires_at')
                           ->orWhere('subscription_expires_at', '>=', now());
                  });
            });
            if ($user && $user->role === 'vendor') {
                $query->orWhere('user_id', $user->id);
            }
        });
    }
}
