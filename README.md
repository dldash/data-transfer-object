# 💡 Data transfer objects

[![Latest Version][ico-version]][url-releases]
[![License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][url-packagist]
[![Build][ico-build]][url-build]

## ⚡️ Requirements

* PHP 8.0

## 💥 Installation

```bash
composer require dldash/data-transfer-object
```

## ✨ Usage

### Simple DTO

If extra fields are passed that are not described in the DTO class, they will be ignored.

DTO class:

```php
use Dldash\DataTransferObject\Models\DataTransferObject;

class UserDto extends DataTransferObject
{
    public function __construct(
        public int $userId,
        public string|null $username
    ) {}
}
```

Usage:

```php
$request = [
    'userId' => 100,
    'username' => 'admin',
    'emailAddress' => 'admin@test.com'
];

$dto = UserDto::create($request);
```

### Value Objects

You can also use value objects in DTO classes.  
All you need is to implement the `ValueObjectContract` interface.

Value object class:

```php
use Dldash\DataTransferObject\Contracts\ValueObjectContract;

class EmailAddress implements ValueObjectContract, JsonSerializable
{
    public function __construct(private string $emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email address [{$emailAddress}] is not valid.");
        }

        $this->emailAddress = strtolower($emailAddress);
    }

    public function value(): string
    {
        return $this->emailAddress;
    }

    public function jsonSerialize(): string
    {
        return $this->emailAddress;
    }
}
```

DTO class:

```php
use Dldash\DataTransferObject\Models\DataTransferObject;

class OrderDto extends DataTransferObject
{
    public function __construct(
        public int $orderId,
        public EmailAddress $emailAddress
    ) {}
}
```

Usage:

```php
$request = [
    'orderId' => 100,
    'emailAddress' => 'admin@test.com'
];

$dto = OrderDto::create($request);
```

### Nested DTO classes

DTO class:

```php
use Dldash\DataTransferObject\Models\DataTransferObject;

class OrderDto extends DataTransferObject
{
    public function __construct(
        public int $orderId,
        public UserDto $user
    ) {}
}
```

Usage:

```php
$request = [
    'orderId' => 100,
    'user' => [
        'userId' => 100,
        'username' => 'admin'
    ]
];

$dto = OrderDto::create($request);
```

### Typed DTO arrays and collections

You can use arrays of DTO objects.  
To do this, you need to inherit the abstract `DataTransferObjectCollection` class.

Collection class:

```php
use Dldash\DataTransferObject\Objects\DataTransferObjectCollection;

/** @method ArrayIterator|UserDto[] getIterator() */
class UserDtoCollection extends DataTransferObjectCollection
{
    protected function create(mixed $item): object
    {
        return UserDto::create($item);
    }
}
```

DTO class:

```php
use Dldash\DataTransferObject\Models\DataTransferObject;

class OrderDto extends DataTransferObject
{
    public function __construct(
        public int $orderId,
        public UserDtoCollection $users
    ) {}
}
```

Usage:

```php
$request = [
    'orderId' => 100,
    'users' => [
        [
            'userId' => 100,
            'username' => 'admin'
        ],
        [
            'userId' => 200,
            'username' => null
        ]
    ]
];

$dto = OrderDto::create($request);
```

### Partial update

Let's imagine that we need to update some model, but we want to do a partial update.
In this case, not all the required fields can be passed to the DTO class.
You can add the `Undefined` type to the desired field.

NOTE: If you pass a `null` value, it will also be `null`.

DTO class:

```php
use Dldash\DataTransferObject\Objects\Undefined;
use Dldash\DataTransferObject\Models\DataTransferObject;

class OrderDto extends DataTransferObject
{
    public function __construct(
        public int $orderId,
        public string|null|Undefined $name
    ) {}
}
```

Usage:

```php
use Dldash\DataTransferObject\Objects\Undefined;

$request = [
    'orderId' => 100
];

$dto = OrderDto::create($request);

if (Undefined::isPresent($dto->name)) {
    // Update this field
}
```

## 💫 Testing

```bash
composer test
```

[ico-version]: https://img.shields.io/packagist/v/dldash/data-transfer-object.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dldash/data-transfer-object.svg?style=flat-square
[ico-build]: https://github.com/dldash/data-transfer-object/workflows/build/badge.svg

[url-packagist]: https://packagist.org/packages/dldash/data-transfer-object
[url-releases]: https://github.com/dldash/data-transfer-object/releases
[url-build]: https://github.com/dldash/data-transfer-object/actions
