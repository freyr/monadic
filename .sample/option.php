<?php

declare(strict_types=1);

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;

require_once __DIR__ . '/../vendor/autoload.php';


function findUser(int $id): Option
{
    $fakeDb = [1 => 'Alice', 2 => 'Bob'];
    if (isset($fakeDb[$id])) {
        return new Some(new User($id, $fakeDb[$id]));
    }
    return new None();
}

/**
 * @param User $user
 * @return Result
 */
function sendWelcomeEmail(User $user): Result
{
    if (rand(1, 5) === 1) {
        return new Err('Email service error for user #' . $user->id);
    }
    return new Ok('Welcome email sent to ' . $user->name);
}


function check(): void
{
    $userId = 2;
    $userOption = findUser($userId);
    $adultUserOption = $userOption->flatMap(fn(User $u): Option => (($u->age ?? 0) >= 18 ? new Some($u) : new None()));
    $mailActionResultOption = $adultUserOption->map(fn(User $u): Result => sendWelcomeEmail($u));
    $endResult = $mailActionResultOption->getOrElse(new Err('User not found or not eligible'));
    $output = $endResult->map(fn(string $msg): string => strtoupper($msg));

    $msg = match (get_class($output)) {
        Ok::class => "Success: {$output->get()}",
        Err::class => "Failure: {$output->getError()}"
    };
    echo $msg . PHP_EOL;

    $msg = $output->fold(
        fn(string $msg): string => 'Success: ' . $msg,
        fn(string $msg): string => 'Failure: ' . $msg
    );
    echo $msg . PHP_EOL;
}

function cascadeCheck(): void
{
    $userId = 2;
    $result = findUser($userId)
        ->flatMap(fn(User $u): Option => (($u->age ?? 0) >= 18 ? new Some($u) : new None()))
        ->map(fn(User $u): Result => sendWelcomeEmail($u))
        ->getOrElse(new Err('User not found or not eligible'))
        ->map(fn(string $msg): string => strtoupper($msg))
        ->fold(
            fn(string $msg): string => 'Success: ' . $msg,
            fn(string $msg): string => 'Failure: ' . $msg
        );
    echo $result . PHP_EOL;
}

// Call the cascadeCheck function to demonstrate its functionality
cascadeCheck();