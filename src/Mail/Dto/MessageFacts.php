<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail\Dto;

/**
 * What the platform established about one incoming message — read from the mailbox and the
 * headers, never from what a model retold.
 *
 * A package gets the facts, not the mailbox: it can judge a message, it cannot read another
 * one. `bodyExcerpt` is the untrusted text of the newest part without quoted history; it is
 * data to be matched, never instructions.
 */
final readonly class MessageFacts
{
    /**
     * @param list<MessageLink>    $links
     * @param list<AttachmentInfo> $attachments
     */
    public function __construct(
        public string $fromAddress,
        public string $displayName,
        public ?string $envelopeFrom,
        public ?string $replyTo,
        public AuthenticationResults $authentication,
        public SenderDomainKind $senderDomainKind,
        public string $subject,
        public string $bodyExcerpt,
        public array $links,
        public array $attachments,
        public \DateTimeImmutable $receivedAt,
        public bool $answeredByUs,
        public bool $repeatedFromSender,
    ) {
    }

    public function fromDomain(): string
    {
        $at = strrpos($this->fromAddress, '@');

        return false === $at ? '' : strtolower(substr($this->fromAddress, $at + 1));
    }
}
