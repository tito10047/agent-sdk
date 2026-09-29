<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * What an agent may know about one of its own concluded runs.
 *
 * A read-only view rather than the platform's run record: the run is the audit trail, it
 * is complete and paid for by the time an agent sees it, and nothing an agent does may
 * change what it says. Everything here is a snapshot of a terminal state — succeeded,
 * failed or cancelled, which one is {@see self::status()}.
 */
interface FinishedRunInterface
{
    public function id(): int;

    /** The code of the agent this run belongs to — always the agent being called. */
    public function agentCode(): string;

    /** Always terminal; {@see RunStatus::isFinished()} is true. */
    public function status(): RunStatus;

    public function trigger(): RunTrigger;

    /**
     * The inputs the run was started with, keyed by input field name.
     *
     * @return array<string, mixed>
     */
    public function inputSnapshot(): array;

    /**
     * The parsed structured answer, or null when the run failed or answers free text.
     *
     * @return array<string, mixed>|null
     */
    public function output(): ?array;

    public function outputText(): ?string;

    public function errorMessage(): ?string;

    public function finishedAt(): ?\DateTimeImmutable;

    /**
     * The files the run handed over as its result, oldest first.
     *
     * @return list<RunFileView>
     */
    public function files(): array;
}
