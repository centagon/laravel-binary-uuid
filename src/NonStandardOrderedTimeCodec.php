<?php
namespace Spatie\BinaryUuid;

use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class NonStandardOrderedTimeCodec extends StringCodec
{
    /**
     * Returns a binary string representation of a UUID, with the timestamp
     * fields rearranged for optimized storage
     *
     * @inheritDoc
     * @psalm-return non-empty-string
     * @psalm-suppress MoreSpecificReturnType we know that the retrieved `string` is never empty
     * @psalm-suppress LessSpecificReturnStatement we know that the retrieved `string` is never empty
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        $bytes = $uuid->getFields()->getBytes();

        return $bytes[6] . $bytes[7]
            . $bytes[4] . $bytes[5]
            . $bytes[0] . $bytes[1] . $bytes[2] . $bytes[3]
            . substr($bytes, 8);
    }

    /**
     * Returns a UuidInterface derived from an ordered-time binary string
     * representation
     *
     * @throws InvalidArgumentException if $bytes is an invalid length
     *
     * @inheritDoc
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException(
                '$bytes string should contain 16 characters.'
            );
        }

        // Rearrange the bytes to their original order.
        $rearrangedBytes = $bytes[4] . $bytes[5] . $bytes[6] . $bytes[7]
            . $bytes[2] . $bytes[3]
            . $bytes[0] . $bytes[1]
            . substr($bytes, 8);

        return parent::decodeBytes($rearrangedBytes);
    }
}
