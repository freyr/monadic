<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Service;

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;

class UserService
{
    public function findUser(int $id): Option
    {
        $fakeDb = [1 => 'Alice', 2 => 'Bob'];
        if (isset($fakeDb[$id])) {
            return new Some(new User($id, $fakeDb[$id]));
        }
        return new None();
    }

    public function sendWelcomeEmail(User $user, bool $forceError = false): Result
    {
        if ($forceError) {
            return new Err('Email service error for user #' . $user->id);
        }
        return new Ok('Welcome email sent to ' . $user->name);
    }
}
