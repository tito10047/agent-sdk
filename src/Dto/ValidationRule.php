<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Dto;

use Leoness\AgentSdk\ValidationVerdict;

/**
 * One domain rule the model's answer has to satisfy, written as an
 * ExpressionLanguage expression over the parsed output.
 *
 * Rules are data on the agent version, not code, so tightening what an agent may claim
 * never means a deploy.
 *
 * Each rule also says what breaking it is worth: *escalate* — the default — means the
 * next model on the ladder gets a chance, *fail* means the run ends here. That is a
 * money decision and it belongs to whoever wrote the rule: "the description mentions
 * the brand" is worth a second, stronger attempt; "the answer is in Slovak" usually is
 * not, and paying three models to fail the same way is how a cost dashboard fills up
 * with nothing.
 */
final readonly class ValidationRule
{
    public function __construct(
        public string $expression,
        public string $message,
        public ValidationVerdict $onFailure = ValidationVerdict::Escalate,
    ) {
        if ('' === trim($this->expression)) {
            throw new \InvalidArgumentException('Validačné pravidlo bez výrazu nemá čo kontrolovať.');
        }

        if ('' === trim($this->message)) {
            throw new \InvalidArgumentException(\sprintf('Validačné pravidlo "%s" nemá hlášku; bez nej sa nedá povedať, prečo beh zlyhal.', $this->expression));
        }

        if (ValidationVerdict::Pass === $this->onFailure) {
            throw new \InvalidArgumentException(\sprintf('Validačné pravidlo "%s" by pri porušení nespravilo nič; potom ho netreba.', $this->expression));
        }
    }

    /**
     * @param array<string, mixed> $data as produced by {@see self::toArray()}
     */
    public static function fromArray(array $data): self
    {
        $expression = $data['expression'] ?? null;
        $message = $data['message'] ?? null;

        if (!\is_string($expression) || !\is_string($message)) {
            throw new \InvalidArgumentException('Validačné pravidlo agenta je poškodené.');
        }

        // Rules written before severities existed escalate, which is what they did.
        $onFailure = ValidationVerdict::tryFrom(\is_string($data['onFailure'] ?? null) ? $data['onFailure'] : '')
            ?? ValidationVerdict::Escalate;

        return new self($expression, $message, $onFailure);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'expression' => $this->expression,
            'message' => $this->message,
            'onFailure' => $this->onFailure->value,
        ];
    }
}
