<?php

namespace ElyAccount\Domain\Identity;

use ElyAccount\Domain\Identity\Exception\InvalidUuidStringException;
use ElyAccount\Domain\Identity\Exception\UuidException;
use Ramsey\Uuid\Uuid as BaseUuid;

class Uuid implements IdentifiesEntities, GeneratesIdentities
{
    /**
     * @var BaseUuid
     */
    private $uuid;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidUuidStringException
     */
    public static function fromString(string $uuidAsAString)
    {
        if (!BaseUuid::isValid($uuidAsAString)) {
            throw UuidException::becauseAStringIsNotAValidUuid($uuidAsAString);
        }

        return new static(BaseUuid::fromString($uuidAsAString));
    }

    /**
     * Generates a Uuid.
     *
     * @return static
     */
    public static function generate()
    {
        return new static(BaseUuid::uuid4());
    }

    /**
     * {@inheritdoc}
     */
    public function equals($other): bool
    {
        if (!($other instanceof static)) {
            return false;
        }

        return $other->uuid->equals($this->uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->uuid;
    }

    /**
     * Initializes an uuid.
     *
     * @param BaseUuid $uuid
     */
    private function __construct(BaseUuid $uuid)
    {
        $this->uuid = $uuid;
    }
}
