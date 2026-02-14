<?php

declare(strict_types=1);

use PhpJson\Adapters\JsonAdapter;

require __DIR__ . '/../vendor/autoload.php';

final class User
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly Order $order,
    ) {}
}

final class Order
{
    public function __construct(
        public readonly string $id,
        public readonly int $amount,
        public readonly ProductCollection $productCollection // Also test with primitive array.
    ) {}
}

final class ProductCollection implements
    \ArrayAccess,
    \Countable,
    \IteratorAggregate
{
    public function __construct(public readonly array $products) {}
    public function offsetExists(mixed $offset): bool
    {
        return (bool) $this->products[$offset];
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->products[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->products[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void {}

    public function count(): int
    {
        return \count($this->products);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->products);
    }
}

// /** @var \PhpJson\Marshals\Json\MakeMarshal */
// $json_make_file = JsonAdapter::getMarshall('user');

// $json_make_file->addValue('name', 'Prosper Pepple')
//     ->addValue('email', 'prosperpepple12@mailer')
//     ->store();

// $order = new \Order(
//     'i12',
//     200_000,
//     new \ProductCollection(['ice_cream', 'beans', 'shirt'])
// );
// $user = new User('Prosper Pepple', 'prosperpepple12@gmail.com', $order);
// $json_make_file->parseFrom($user);

/** @var \PhpJson\Marshals\Json\EditMarshal */
$json_edit_file = JsonAdapter::getMarshall('user');
$json_edit_file->setValue('name', 'Adam Pierce')
    ->setValue('email', 'adampierce@mail.com')
    // ->appendValue('age', 47)
    ->removeValue('order[productCollection][products][0]')
    ->store();

// // If along the way an issue occurs, the file is reverted to the way it was, or deleted if it wasn't
// // The EditMarshal calls a read/write lock on the file for either reading or writing .

// $user = $json_edit_file->parseTo(User::class); // Cannot modify file until reset() is called.
// $user = $json_edit_file->get();
// $user = $json_edit_file->getAssociative(); // not inherited

// // The reset is used to cancel operations along the pipeline
// $json_edit_file->appendValue('password', 'oaheisj');
// if (1) {
//     $json_edit_file->reset(); // Reverts all operations since last store() call.
// }
// $json_edit_file->store();
