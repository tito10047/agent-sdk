<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Leoness\AgentSdk\Dto\FactClaim;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * What one property of an agent's answer looks like.
 *
 * This is the vocabulary the builder offers instead of a JSON textarea. The point is not
 * convenience: a schema assembled from these cases cannot break the rules of strict
 * structured output — an object root, every property described, nothing else allowed,
 * everything required — so the class of mistake that only shows up on the first item of a
 * five-hundred-item batch is unrepresentable rather than validated after the fact.
 *
 * {@see self::Claim} is the one that matters most. It emits the {@see FactClaim} fragment,
 * which is how section 11 of the brief is enforced at all: the model is asked for "value,
 * how well backed, by what" at exactly the point where it would otherwise invent a
 * dimension. Everything else is a plain value the platform cannot police, which is a
 * decision the agent author makes knowingly.
 */
enum OutputFieldKind: string implements TranslatableInterface
{
    /** Value + status + source, the convention of {@see FactClaim}. */
    case Claim = 'claim';

    case Text = 'text';

    /** A list of strings — bullet points, keywords, alternatives. */
    case TextList = 'text_list';

    case Number = 'number';

    case Boolean = 'boolean';

    /**
     * The JSON schema fragment for one property of this kind, ready to be dropped into
     * the `properties` of an output schema.
     *
     * @return array<string, mixed>
     */
    public function schema(?string $description): array
    {
        if (self::Claim === $this) {
            // The claim fragment always carries its description: it is the instruction the
            // model reads at the point of writing the value, not documentation.
            return FactClaim::schema($description ?? 'Údaj, ktorý si nesmieš vymyslieť.');
        }

        // Claim is returned above, so it deliberately has no arm here: an unreachable one
        // would only be code nobody can ever test.
        $schema = match ($this) {
            self::Text => ['type' => 'string'],
            self::TextList => ['type' => 'array', 'items' => ['type' => 'string']],
            self::Number => ['type' => 'number'],
            self::Boolean => ['type' => 'boolean'],
        };

        if (null !== $description && '' !== $description) {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /**
     * Reads a fragment back into a kind, or null when it is something the builder does not
     * have a widget for.
     *
     * Only a guess: whether the kind really reproduces the fragment is decided by
     * {@see Dto\OutputSchemaPlan::fromSchema()}, which re-emits the whole
     * schema and compares. That is what keeps the editor from quietly dropping a
     * `maxLength` it does not understand.
     *
     * @param array<string, mixed> $schema
     */
    public static function of(array $schema): ?self
    {
        if (FactClaim::describedBy($schema)) {
            return self::Claim;
        }

        $items = $schema['items'] ?? null;

        return match ($schema['type'] ?? null) {
            'string' => self::Text,
            'number' => self::Number,
            'boolean' => self::Boolean,
            'array' => \is_array($items) && 'string' === ($items['type'] ?? null) ? self::TextList : null,
            default => null,
        };
    }

    /**
     * The human name of the kind, in the language of the platform's operators.
     *
     * A plain string on top of {@see self::trans()} so that SDK-side code — the lint
     * that has no translator — can name a kind in a sentence.
     */
    public function label(): string
    {
        return match ($this) {
            self::Claim => 'Overiteľný údaj (hodnota + stav + zdroj)',
            self::Text => 'Text',
            self::TextList => 'Zoznam textov',
            self::Number => 'Číslo',
            self::Boolean => 'Áno / nie',
        };
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->label();
    }
}
