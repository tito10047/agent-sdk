<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Confirmation\Dto;

/**
 * What happened when a confirmed action was carried out, in a sentence for the person and
 * for the model that asks afterwards.
 *
 * **Both are readers outside the server**, so the message must never carry a path, a
 * setting or an exception text. An executor that fails for a technical reason logs the
 * cause and answers {@see self::GENERIC_FAILURE}.
 */
final readonly class ActionOutcome
{
    public const string GENERIC_FAILURE = 'Akciu sa nepodarilo vykonať pre technickú chybu na strane platformy. Podrobnosti sú v protokole servera; obráťte sa na administrátora.';

    private function __construct(
        public bool $succeeded,
        public string $message,
    ) {
    }

    public static function done(string $message): self
    {
        return new self(true, $message);
    }

    public static function failed(string $message): self
    {
        return new self(false, $message);
    }
}
