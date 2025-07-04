<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\V1\Admin\UserService;
use App\Http\Resources\V1\Auth\UserResource;
use App\Http\Requests\V1\Admin\SearchUserRequest;

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
