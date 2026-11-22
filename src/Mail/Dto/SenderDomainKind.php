<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail\Dto;

/**
 * What the platform alone can tell about the sender's domain, without any company's list
 * of customers and suppliers — that list is a package's judgement, not the platform's.
 */
enum SenderDomainKind: string
{
    /** The domain of the mailbox the message arrived to. */
    case Own = 'vlastna';

    /** A public mail provider (gmail.com, azet.sk, …): anybody can have an address there. */
    case Freemail = 'freemail';

    case Other = 'ina';
}
