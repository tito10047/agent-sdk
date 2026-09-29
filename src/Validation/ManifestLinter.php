<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

use Leoness\AgentSdk\Dto\AgentManifest;

/**
 * Everything that can be said against a manifest without asking the platform.
 *
 * This is the shared half of the contract between an agent repository and the core: the
 * repository's deployability test runs it in CI, the platform runs the same checks (and
 * its own — whether whitelisted tools exist, for one) at installation. What passes here
 * can still be refused there, but never the other way around for these rules.
 */
final readonly class ManifestLinter
{
    /**
     * Matches every `{{ variable }}` a prompt template addresses, tolerating filters
     * (`{{ sku|upper }}`) but nothing an input field name could not be.
     */
    private const string PROMPT_VARIABLE = '/\{\{-?\s*([a-zA-Z_][a-zA-Z0-9_]*)/';

    public function __construct(
        private OutputSchemaRules $schemaRules = new OutputSchemaRules(),
        private RuleExpressionLanguage $expressions = new RuleExpressionLanguage(new RuleFunctions()),
    ) {
    }

    /**
     * @return list<ManifestProblem> empty when the manifest is installable as far as
     *                               static checking can tell
     */
    public function lint(AgentManifest $manifest): array
    {
        $problems = [];

        if ('' === trim($manifest->code)) {
            $problems[] = new ManifestProblem('code', 'agent nemá kód; bez neho sa nedá evidovať ani spustiť');
        }

        if ('' === trim($manifest->name)) {
            $problems[] = new ManifestProblem('name', 'agent nemá meno');
        }

        if ('' === trim($manifest->promptTemplate)) {
            $problems[] = new ManifestProblem('promptTemplate', 'prompt je prázdny');
        }

        $problems = [...$problems, ...$this->inputFieldProblems($manifest)];

        $schemaProblem = $this->schemaRules->firstProblem($manifest->outputSchema);

        if (null !== $schemaProblem) {
            $problems[] = new ManifestProblem('outputSchema/'.$schemaProblem->path, $schemaProblem->reason);
        }

        foreach ($manifest->validationRules as $index => $rule) {
            $reason = $this->expressions->reasonItCannotRun($rule->expression);

            if (null !== $reason) {
                $problems[] = new ManifestProblem(
                    \sprintf('validationRules[%d]', $index),
                    \sprintf('výraz "%s" sa nedá vyhodnotiť: %s', $rule->expression, $reason),
                );
            }
        }

        return $problems;
    }

    /**
     * @return list<ManifestProblem>
     */
    private function inputFieldProblems(AgentManifest $manifest): array
    {
        $problems = [];
        $declared = [];

        foreach ($manifest->inputFields as $field) {
            if (\in_array($field->name, $declared, true)) {
                $problems[] = new ManifestProblem(
                    \sprintf('inputFields[%s]', $field->name),
                    'vstupné pole je deklarované dvakrát',
                );
            }

            $declared[] = $field->name;
        }

        preg_match_all(self::PROMPT_VARIABLE, $manifest->promptTemplate, $matches);

        foreach (array_unique($matches[1]) as $variable) {
            if (!\in_array($variable, $declared, true)) {
                $problems[] = new ManifestProblem(
                    'promptTemplate',
                    \sprintf('prompt používa premennú "%s", ktorú žiadne vstupné pole nedeklaruje', $variable),
                );
            }
        }

        return $problems;
    }
}
