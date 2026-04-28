<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('username', 'admin')->first();

if (!$user) {
    $user = new User();
    $user->name = 'Admin';
    $user->username = 'admin';
    $user->email = 'admin@example.com';
    $user->phone = '000000000';
    $user->role = 'admin';
    $user->branch_id = 1;
}

$user->status = 'active';
$user->password = Hash::make('password123');
$user->save();

$user;
