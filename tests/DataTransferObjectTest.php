<?php

namespace Dldash\DataTransferObject\Tests;

use Dldash\DataTransferObject\Contracts\DataTransferObjectContract;
use Dldash\DataTransferObject\Objects\Undefined;
use Dldash\DataTransferObject\Tests\DTO\OrderDto;
use Dldash\DataTransferObject\Tests\DTO\UserDto;
use Dldash\DataTransferObject\Tests\Objects\EmailAddress;
use PHPUnit\Framework\TestCase;
use TypeError;

class DataTransferObjectTest extends TestCase
{
    public function test_create(): void
    {
        $request = [
            'orderId' => 1,
            'emailAddress' => 'admin@test.com',
            'user' => [
                'userId' => 100,
                'username' => 'admin'
            ],
            'undefined' => null
        ];

        $dto = OrderDto::create($request);

        $this->assertInstanceOf(UserDto::class, $dto->user);
        $this->assertInstanceOf(EmailAddress::class, $dto->emailAddress);
        $this->assertInstanceOf(DataTransferObjectContract::class, $dto);

        $this->assertEquals(1, $dto->orderId);
        $this->assertEquals('admin@test.com', $dto->emailAddress->value());
        $this->assertEquals(['userId' => 100, 'username' => 'admin'], $dto->user->toArray());

        $this->assertNull($dto->undefined);
    }

    public function test_redundant_fields_are_passed(): void
    {
        $request = [
            'userId' => 100,
            'username' => 'admin',
            'emailAddress' => 'admin@test.com'
        ];

        $dto = UserDto::create($request);

        $this->assertEquals(['userId' => 100, 'username' => 'admin'], $dto->toArray());
    }

    public function test_all_fields_are_not_passed(): void
    {
        $request = [
            'orderId' => 1,
            'emailAddress' => 'admin@test.com',
            'user' => [
                'userId' => 100,
                'username' => 'admin'
            ]
        ];

        $dto = OrderDto::create($request);

        $this->assertInstanceOf(Undefined::class, $dto->undefined);
        $this->assertInstanceOf(Undefined::class, $dto->toArray()['undefined']);

        $this->assertEmpty((string)$dto->undefined);
        $this->assertNull(json_decode(json_encode($dto->undefined)));
    }

    public function test_null_passed(): void
    {
        $this->expectException(TypeError::class);

        $request = [
            'userId' => null,
            'username' => null
        ];

        UserDto::create($request);
    }

    public function test_nullable_values_passed(): void
    {
        $request = [
            'userId' => 100,
            'username' => null
        ];

        $dto = UserDto::create($request);

        $this->assertNull($dto->username);
        $this->assertEquals(['userId' => 100, 'username' => null], $dto->toArray());
    }

    public function test_use_constructor(): void
    {
        $dto = new UserDto(userId: 100, username: null);

        $this->assertNull($dto->username);
        $this->assertEquals(['userId' => 100, 'username' => null], $dto->toArray());
    }

    public function test_is_present(): void
    {
        $this->assertTrue(Undefined::isPresent(''));
        $this->assertTrue(Undefined::isPresent(0));
        $this->assertTrue(Undefined::isPresent([]));
        $this->assertTrue(Undefined::isPresent(null));
        $this->assertTrue(Undefined::isPresent(false));
        $this->assertTrue(Undefined::isPresent(new EmailAddress("admin@test.com")));
        $this->assertFalse(Undefined::isPresent(Undefined::create()));
    }
}
