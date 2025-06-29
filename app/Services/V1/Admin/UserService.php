<?php

namespace App\Services\V1\Admin;

use App\Models\User;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
      public function getAllUsers()
    {
        return User::paginate(50);
    }

  

    public function getAllVendors()
    {
        return User::where('role', 'vendor')->paginate();
    }
    /**
     * General search: by ID, email, or full name
     */
    public function searchUsers(string $query)
    {
        return User::where(function ($q) use ($query) {
                $q->where('email', 'like', "%$query%")
                  ->orWhere('id', $query)
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$query%"]);
            })
            ->paginate();
    }
}
