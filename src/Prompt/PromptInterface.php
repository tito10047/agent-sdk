<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Prompt;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Instructions a package offers to the AI client as an MCP prompt — in Claude it shows up
 * as a command the user picks ("Emailový asistent – konateľ"), not as a hidden system
 * prompt. The platform has no agents to attach instructions to; the user chooses them.
 *
 * Text, not code: rules that must hold still belong in a tool, where the platform enforces
 * them. A prompt says how to work; it cannot be the only thing standing between a mail and
 * a wrong draft. Put the version in {@see text()} so a conversation shows which one it used.
 */
#[AutoconfigureTag(self::TAG)]
interface PromptInterface
{
    public const string TAG = 'leoness.prompt';

    /** snake_case, unique across the platform, e.g. `emailovy_asistent_konatel`. */
    public function name(): string;

    /** One sentence the user reads when choosing the prompt. */
    public function description(): string;

    public function text(): string;
}
