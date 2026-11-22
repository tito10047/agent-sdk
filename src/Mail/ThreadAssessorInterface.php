<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Mail;

use Leoness\AgentSdk\Mail\Dto\MessageFacts;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * A package's judgement of an incoming message — triage, phishing signals, escalation —
 * that the platform runs itself whenever `precitaj_vlakno` hands a thread to the model.
 *
 * Why the platform calls it instead of the model: rules fed by a model can be talked out of
 * a verdict by the very mail they judge ("SPF je pass"). Here the facts come from the
 * mailbox and the verdict goes straight into the trusted part of the answer, next to the
 * sender check, where no message body can reach it.
 *
 * Implementations are tagged automatically; {@see key()} names the section of the answer
 * and must be unique across installed packages. A failing assessor is reported in one
 * sentence and does not break reading the thread.
 */
#[AutoconfigureTag(self::TAG)]
interface ThreadAssessorInterface
{
    public const string TAG = 'leoness.thread_assessor';

    /** snake_case, e.g. `emailovy` — the key the assessment appears under. */
    public function key(): string;

    /**
     * @return array<string, mixed> JSON-serialisable; empty keys stay present so the model
     *                              can tell "not set" from "does not exist"
     */
    public function assess(MessageFacts $message): array;
}
