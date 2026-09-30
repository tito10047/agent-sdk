<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Tool;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/**
 * How a tool talks back to a model.
 *
 * Structured answers go back as JSON with every key present, empty ones included: a model
 * that sees `"material": null` is being told the value is unknown, whereas a model that
 * simply does not see the key is being invited to supply it.
 *
 * Everyday negative answers — nothing found, not yours, not allowed — are sentences and
 * not exceptions. A model told "no such product" stops; a model told nothing improvises,
 * and section 11 of the brief is lost in exactly that gap.
 */
#[Exclude]
final readonly class ToolAnswer
{
    private function __construct()
    {
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function json(array $data): string
    {
        return json_encode($data, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR);
    }

    /**
     * Decodes a JSON object a model sent as a string argument.
     *
     * Objects of a shape that is not known until run time — the input fields of whichever
     * agent is being started — cannot be declared as a tool parameter: the schema derived
     * from a PHP `array` says "array", and a provider enforcing it strictly then refuses
     * the object the model meant to send. A JSON string is the one shape every provider
     * agrees on.
     *
     * @return array<string, mixed>|string the decoded object, or the sentence to hand back
     */
    public static function decodeObject(string $json, string $argument): array|string
    {
        if ('' === trim($json)) {
            return [];
        }

        try {
            $decoded = json_decode($json, true, 32, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return \sprintf('Argument "%s" nie je platný JSON: %s', $argument, $exception->getMessage());
        }

        if (!\is_array($decoded) || array_is_list($decoded)) {
            return \sprintf('Argument "%s" musí byť JSON objekt, napríklad {"nazov":"Stolička"}.', $argument);
        }

        /* @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * Decodes a table a model sent as a string argument: a JSON list of rows, each a list
     * of cells.
     *
     * A string for the same reason {@see self::decodeObject()} is one — a PHP `array`
     * parameter becomes "array" in the schema and a strict provider then refuses the value
     * the model meant to send. Rows are checked one by one, because "the third row is not
     * a list" is something a model can fix and "neplatný argument" is not.
     *
     * @return list<list<scalar|null>>|string the decoded rows, or the sentence to hand back
     */
    public static function decodeRows(string $json, string $argument): array|string
    {
        if ('' === trim($json)) {
            return [];
        }

        try {
            $decoded = json_decode($json, true, 32, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return \sprintf('Argument "%s" nie je platný JSON: %s', $argument, $exception->getMessage());
        }

        if (!\is_array($decoded) || !array_is_list($decoded)) {
            return \sprintf('Argument "%s" musí byť JSON zoznam riadkov, napríklad [["EX134","ks"],["EX112/12","bal"]].', $argument);
        }

        $rows = [];

        foreach ($decoded as $index => $row) {
            if (!\is_array($row) || !array_is_list($row)) {
                return \sprintf('Riadok %d argumentu "%s" musí byť zoznam buniek, napríklad ["EX134","ks",5].', $index + 1, $argument);
            }

            foreach ($row as $position => $cell) {
                if (null !== $cell && !\is_scalar($cell)) {
                    return \sprintf(
                        'Bunka %d v riadku %d argumentu "%s" musí byť text, číslo alebo null — vnorené objekty sa do tabuľky nezapisujú.',
                        $position + 1,
                        $index + 1,
                        $argument,
                    );
                }
            }

            /* @var list<scalar|null> $row */
            $rows[] = $row;
        }

        return $rows;
    }
}
