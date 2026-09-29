<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Validation;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * The engine that evaluates the validation rules of an agent, and the same engine that
 * checks them when they are saved.
 *
 * One instance for both is the point: a rule that the form accepted must not be a rule
 * the batch cannot run. The parsed-expression cache is the other half of it — a batch
 * of five hundred products evaluates the same handful of expressions five hundred times.
 */
final class RuleExpressionLanguage extends ExpressionLanguage
{
    /** The only variables a rule may name. Anything else is a typo and must be caught as one. */
    public const array VARIABLES = ['output', 'text', 'input'];

    public function __construct(RuleFunctions $functions, ?CacheItemPoolInterface $cache = null)
    {
        parent::__construct($cache);

        foreach ($functions->all() as $function) {
            $this->addFunction($function);
        }
    }

    /**
     * Why an expression cannot be used, or null when it can.
     *
     * Returns a reason instead of throwing because both callers — the constraint on the
     * agent version and the diagnostics in the agent builder — want to show it, not
     * handle it.
     */
    public function reasonItCannotRun(string $expression): ?string
    {
        try {
            $this->lint($expression, self::VARIABLES);

            return null;
        } catch (SyntaxError $error) {
            return $error->getMessage();
        }
    }
}
