<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function updateProfile(int $id, array $data): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }
}
