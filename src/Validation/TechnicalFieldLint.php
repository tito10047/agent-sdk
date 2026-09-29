<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

use Leoness\AgentSdk\Dto\OutputField;
use Leoness\AgentSdk\Dto\OutputSchemaPlan;
use Leoness\AgentSdk\OutputFieldKind;

/**
 * Notices output fields that look like technical data but are not declared as claims.
 *
 * The truthfulness rule of section 11 is enforced only where somebody opted into it: the
 * validator checks the fields written in the {@see \Leoness\AgentSdk\Dto\FactClaim}
 * shape and is silent about the rest. So an agent author who declares `sirka_mm` as an
 * ordinary text field has, without doing anything wrong on any screen, produced an agent
 * that may state a width it invented and no part of the platform will object.
 *
 * That is a gap in the *builder*, not in the validator — nothing downstream can tell a
 * width from a slogan — so it is closed where it opens, at the moment somebody saves. A
 * warning and not a refusal: "norma" might be an agent that writes about standards rather
 * than one that quotes them, and a builder that argued with its author about vocabulary
 * would be turned off rather than obeyed.
 */
final readonly class TechnicalFieldLint
{
    /**
     * Word stems that make a field name a technical claim in this business.
     *
     * Stems rather than whole words, so that `sirka`, `sirka_mm` and `vnutorna_sirka` are
     * all caught by one entry. Drawn from what section 11 of the brief actually lists:
     * dimensions, material, weight, packaging, compatibility, certifications, standards.
     *
     * @var list<string>
     */
    private const array STEMS = [
        'sirk', 'vysk', 'dlzk', 'hlbk', 'priemer', 'rozmer', 'rozsah',
        'hmotnos', 'vaha', 'nosnos', 'objem', 'kapacit',
        'material', 'zlozen', 'povrch',
        'balen', 'kusov', 'mnozstv',
        'kompatib', 'certifik', 'norm', 'atest', 'trieda',
        'napati', 'prud', 'vykon', 'prikon', 'spotreb', 'ucinnos',
        'zaruk', 'teplot', 'tlak',
    ];

    /**
     * @return list<string> one sentence per field worth a second look, empty when there is
     *                      nothing to say
     */
    public function check(OutputSchemaPlan $plan): array
    {
        $warnings = [];

        foreach ($plan->fields as $field) {
            if (OutputFieldKind::Claim === $field->kind || !self::soundsTechnical($field)) {
                continue;
            }

            $warnings[] = \sprintf(
                'Pole "%s" vyzerá na technický údaj, ale nie je deklarované ako overiteľný údaj '
                .'(hodnota + stav + zdroj). Model doň smie napísať čokoľvek a kontrola pravdivosti '
                .'sa naň nevzťahuje. Ak je to naozaj technický údaj, prepnite mu typ na „%s“.',
                $field->name,
                OutputFieldKind::Claim->label(),
            );
        }

        return $warnings;
    }

    private static function soundsTechnical(OutputField $field): bool
    {
        $name = $field->name;

        foreach (self::STEMS as $stem) {
            if (str_contains($name, $stem)) {
                return true;
            }
        }

        return false;
    }
}
