<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserServiceInterface.
 *
 * Defines the contract for a User service, including saving users
 * and optionally hashing their plain password.
 */
interface UserServiceInterface
{
    /**
     * Save a user entity and optionally hash a plain password.
     *
     * @param User        $user          The user entity
     * @param string|null $plainPassword Optional plain password to hash
     */
    public function save(User $user, ?string $plainPassword): void;
}
