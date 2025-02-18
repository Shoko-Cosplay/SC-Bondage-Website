<?php

namespace App\Database\Auth\Event;

use App\Database\Auth\User;
use Symfony\Component\HttpFoundation\Request;

class UserBeforeCreatedEvent
{
    public function __construct(
        public readonly User $user,
        public readonly Request $request,
    ) {
    }
}
