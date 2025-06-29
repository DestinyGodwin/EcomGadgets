<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\V1\Services\Admin\UserService;
use App\Http\Resources\V1\Auth\UserResource;

class UserController extends Controller
{
 public function __construct(protected UserService $userService) {}

    public function index()
    {
        $user =  $this->userService->getAllUsers();
        return UserResource::collection($user);
    }

    public function getVendors(){
        $vendors = $this->userService->getAllVendors();
        return UserResource::collection($vendors);
    }
    
    public function search(SearchUserRequest $request)
{
    $query = $request->validated()['q'];
    $users = $this->userService->searchUsers($query);
        return UserResource::collection($users);
}
}
