<?php

declare(strict_types=1);

namespace Freyr\Monadic\Examples;

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;

require __DIR__ . '/../vendor/autoload.php';

// Run it with: composer example
// The script prints each line, then compares the lines with a list written by hand.
// It exits with 1 when they differ, so the exit code can serve as a check in CI.

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $age,
    ) {
    }
}

/**
 * @return Option<User>
 */
function findUser(int $id): Option
{
    $users = [
        1 => new User(1, 'Alice', 30),
        2 => new User(2, 'Bob', 17),
        3 => new User(3, 'Carol', 25),
    ];

    return isset($users[$id]) ? new Some($users[$id]) : new None();
}

/**
 * @return Option<User>
 */
function onlyAdult(User $user): Option
{
    return $user->age >= 18 ? new Some($user) : new None();
}

/**
 * The mail service rejects user 3, so the example has a failure to show.
 *
 * @return Result<string, string>
 */
function sendWelcomeEmail(User $user): Result
{
    if ($user->id === 3) {
        return new Err('Email service error for user #' . $user->id);
    }

    return new Ok('Welcome email sent to ' . $user->name);
}

/**
 * @return Result<string, string>
 */
function confirmDelivery(string $message): Result
{
    return new Ok($message . ' (delivery confirmed)');
}

/**
 * The cascade: find the user, keep adults only, send the email,
 * fall back to an Err when no user qualifies, and fold to a message.
 */
function welcome(int $id): string
{
    return findUser($id)
        ->flatMap(onlyAdult(...))
        ->map(sendWelcomeEmail(...))
        ->getOrElse(new Err('User not found or not eligible'))
        ->map(strtoupper(...))
        ->fold(
            fn (string $message): string => 'Success: ' . $message,
            fn (string $error): string => 'Failure: ' . $error,
        );
}

$lines = [];

// Option::map and Option::getOrElse (the default is returned as given).
$lines[] = 'Name of user 1: ' . findUser(1)->map(fn (User $user): string => $user->name)->getOrElse('nobody');
$lines[] = 'Name of user 99: ' . findUser(99)->map(fn (User $user): string => $user->name)->getOrElse('nobody');

// Option::getOrCall (the default is a function, called only when the Option is empty).
$lines[] = 'Name of user 99, lazy: ' . findUser(99)
    ->map(fn (User $user): string => $user->name)
    ->getOrCall(fn (): string => 'guest');

// Option::flatMap: the function returns an Option, so the result is not nested.
$lines[] = 'Adult 1: ' . findUser(1)->flatMap(onlyAdult(...))->map(fn (User $user): string => $user->name)->getOrElse('none');
$lines[] = 'Adult 2: ' . findUser(2)->flatMap(onlyAdult(...))->map(fn (User $user): string => $user->name)->getOrElse('none');

// Result::flatMap: the function returns a Result, so a failure stops the chain.
$confirmed = sendWelcomeEmail(new User(1, 'Alice', 30))->flatMap(confirmDelivery(...));
$lines[] = $confirmed->fold(
    fn (string $message): string => 'Confirmed: ' . $message,
    fn (string $error): string => 'Not confirmed: ' . $error,
);
$rejected = sendWelcomeEmail(new User(3, 'Carol', 25))->flatMap(confirmDelivery(...));
$lines[] = $rejected->fold(
    fn (string $message): string => 'Confirmed: ' . $message,
    fn (string $error): string => 'Not confirmed: ' . $error,
);

// The whole cascade: success, user not found, user too young, email failure.
$lines[] = 'User 1: ' . welcome(1);
$lines[] = 'User 99: ' . welcome(99);
$lines[] = 'User 2: ' . welcome(2);
$lines[] = 'User 3: ' . welcome(3);

foreach ($lines as $line) {
    echo $line . PHP_EOL;
}

$expected = [
    'Name of user 1: Alice',
    'Name of user 99: nobody',
    'Name of user 99, lazy: guest',
    'Adult 1: Alice',
    'Adult 2: none',
    'Confirmed: Welcome email sent to Alice (delivery confirmed)',
    'Not confirmed: Email service error for user #3',
    'User 1: Success: WELCOME EMAIL SENT TO ALICE',
    'User 99: Failure: User not found or not eligible',
    'User 2: Failure: User not found or not eligible',
    'User 3: Failure: Email service error for user #3',
];

if ($lines !== $expected) {
    fwrite(STDERR, 'The output of the example differs from the expected lines.' . PHP_EOL);
    exit(1);
}
