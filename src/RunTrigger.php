<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * What caused a run to exist.
 *
 * Kept on the run because the cost dashboard has to be able to tell a five-euro
 * batch apart from a builder test-run that cost two cents, and because a run with
 * no user attached is only legitimate when it came from a queue.
 */
enum RunTrigger: string implements TranslatableInterface
{
    /** A single run started by a person from the administration. */
    case Manual = 'manual';

    /** One item of a bulk job, dispatched through the queue. */
    case Batch = 'batch';

    /** A dry run from the agent builder — same code path, different intent. */
    case Test = 'test';

    /**
     * One turn of a conversation in the chat window.
     *
     * Told apart from {@see self::Manual} because a chat turn is a run somebody is
     * waiting in front of, its cost belongs to the free-form half of the bill rather
     * than to a product being processed, and a notification about it would be noise —
     * the answer is already on the screen that asked for it.
     */
    case Chat = 'chat';

    /**
     * Work another run started — the chat asking a specialised agent to do something.
     *
     * Told apart from {@see self::Chat} because that case means "this run *is* a turn of a
     * conversation", and two things follow from it that must not follow here: the answer is
     * already on the screen (so no notification), and the person's last message counts as a
     * confirmation. A delegated run finishes minutes later in another process, so it has to
     * report back into the conversation,
     * and a "POTVRDZUJEM" typed before it started authorises nothing inside it.
     *
     * Which conversation it came out of is a question for the platform record of the run, not this.
     */
    case Delegated = 'delegovany';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Manual => 'Ručne',
            self::Batch => 'Dávka',
            self::Test => 'Test',
            self::Chat => 'Chat',
            self::Delegated => 'Delegované',
        };
    }
}
