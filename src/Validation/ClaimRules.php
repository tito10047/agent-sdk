<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

use Leoness\AgentSdk\Dto\FactClaim;
use Leoness\AgentSdk\FactStatus;

/**
 * What is wrong with one {@see FactClaim}, if anything.
 *
 * **The rules moved here because there are now two callers and only one may have them.**
 * The platform's output validator walks an agent's declared output schema, so it only ever
 * sees claims in an *answer*; a tool argument never passes through it. `vytvor_ponuku`
 * takes a unit price and a list of parameters as claims, and those have to be held to the
 * same rules — a price marked `potvrdeny` with no source is the same mistake whether it
 * arrives in an answer or in a tool call. Two copies of the rules would have been two
 * answers to that, and the one nobody updated would be the one guarding the offer.
 *
 * In the SDK rather than in the core, because the agent packages run their own deployability
 * tests without a platform: a package can check its fixtures against the real rules.
 *
 * **What is *not* here is policy.** These are the rules of the convention itself — a
 * confirmed value must have a value and a source, a suggestion must have a value, an
 * unknown must not. What to *do* about a well-formed `navrh_ai` differs by field and by
 * domain: a proposed parameter is left out of an offer and reported back, a proposed price
 * is a refusal, and a proposed product description is simply shown for approval. That
 * decision belongs to whoever is holding the claim.
 */
final readonly class ClaimRules
{
    public const string MALFORMED = 'truthfulness.malformed';
    public const string EMPTY_CONFIRMATION = 'truthfulness.empty_confirmation';
    public const string CONFIRMED_WITHOUT_SOURCE = 'truthfulness.confirmed_without_source';
    public const string EMPTY_SUGGESTION = 'truthfulness.empty_suggestion';
    public const string UNCONFIRMED_WITH_VALUE = 'truthfulness.unconfirmed_with_value';

    /**
     * @param FactClaim|null $claim null for a value that does not follow the convention
     *                              at all — which is itself a problem, never silence
     *
     * @return array{code: string, message: string}|null null when the claim is internally
     *                                                   consistent
     */
    public function problemOf(?FactClaim $claim): ?array
    {
        if (null === $claim) {
            return [
                'code' => self::MALFORMED,
                'message' => \sprintf(
                    'Údaj nie je zapísaný podľa konvencie — chýba mu "%s" s jednou z hodnôt %s.',
                    FactClaim::STATUS,
                    implode(', ', FactStatus::values()),
                ),
            ];
        }

        return match ($claim->status) {
            FactStatus::Confirmed => match (true) {
                !$claim->hasValue() => [
                    'code' => self::EMPTY_CONFIRMATION,
                    'message' => 'Údaj je označený ako potvrdený, ale nemá hodnotu.',
                ],
                !$claim->hasSource() => [
                    'code' => self::CONFIRMED_WITHOUT_SOURCE,
                    'message' => 'Údaj je označený ako potvrdený, ale neuvádza zdroj — potvrdiť sa dá len to, čo je dohľadateľné.',
                ],
                default => null,
            },
            FactStatus::Suggested => $claim->hasValue() ? null : [
                'code' => self::EMPTY_SUGGESTION,
                'message' => \sprintf(
                    'Údaj je označený ako návrh AI, ale nemá hodnotu. Neznámy údaj patrí do stavu "%s".',
                    FactStatus::Unknown->value,
                ),
            ],
            FactStatus::Unknown => $claim->hasValue() ? [
                'code' => self::UNCONFIRMED_WITH_VALUE,
                'message' => \sprintf(
                    'Údaj je označený ako nepotvrdený, ale nesie hodnotu "%s". Nepotvrdený údaj musí mať hodnotu null.',
                    $claim->value,
                ),
            ] : null,
        };
    }
}
