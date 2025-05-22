<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Service;

use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;
use PHPUnit\Framework\TestCase;

final class UserServiceTest extends TestCase
{
    public function testFindUserReturnsSome(): void
    {
        $userService = new UserService();
        $result = $userService->findUser(1);
        self::assertInstanceOf(Some::class, $result);
    }

    public function testFindUserReturnsNone(): void
    {
        $userService = new UserService();
        $result = $userService->findUser(99);
        self::assertInstanceOf(None::class, $result);
    }

    public function testSendWelcomeEmailReturnsOk(): void
    {
        $userService = new UserService();
        $user = new User(1, 'Alice');
        $result = $userService->sendWelcomeEmail($user);
        self::assertInstanceOf(Ok::class, $result);
    }

    public function testSendWelcomeEmailReturnsErr(): void
    {
        $userService = new UserService();
        $user = new User(1, 'Alice');
        $result = $userService->sendWelcomeEmail($user, true);
        self::assertInstanceOf(Err::class, $result);
    }
}
