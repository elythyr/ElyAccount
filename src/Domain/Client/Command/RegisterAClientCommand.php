<?php

namespace ElyAccount\Client\Command;

use ElyAccount\Client\ClientName;
use ElyAccount\Client\ClientId;
use ElyAccount\Command\Command;

/**
 * Commands asking to sign up a new client.
 *
 * @see Command
 * @final
 */
final class RegisterAClientCommand implements Command
{
    /**
     * @var ClientId
     */
    private $clientId;

    /**
     * @var ClientName
     */
    private $clientName;

    /**
     * Prepares the command.
     *
     * @param ClientId $clientId
     * @param ClientName $clientName
     *
     * @return self
     */
    public static function prepare(ClientId $clientId, ClientName $clientName): self
    {
        return new self($clientId, $clientName);
    }

    /**
     * Initializes the command.
     *
     * @param ClientId $clientId
     * @param ClientName $clientName
     */
    private function __construct(ClientId $clientId, ClientName $clientName)
    {
        $this->clientId   = $clientId;
        $this->clientName = $clientName;
    }

    /**
     * Gets the client identity.
     *
     * @return ClientId
     */
    public function clientId(): ClientId
    {
        return $this->clientId;
    }

    /**
     * Gets the client client name.
     *
     * @return ClientName
     */
    public function clientName(): ClientName
    {
        return $this->clientName;
    }
}
