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
    'orderID' => 100,
    'emailAddress' => 'admin@test.com'
];

$dto = OrderDto::create($request);
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
