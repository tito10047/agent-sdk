<?php

declare(strict_types=1);

namespace Leoness\AgentSdk\Tool;

/**
 * Whether a person — not the model — has agreed to what is about to happen.
 *
 * Several tools carry a sentence in their description asking the model to get confirmation
 * before doing something irreversible or bulk. That sentence is worth having and is worth
 * nothing on its own: the text that talks a model into calling a tool arrives through the
 * same channel as the text asking it to be careful, and an attacker who can put a line in
 * a supplier's spreadsheet can write both. A model asked to confirm with the user can be
 * told it already did.
 *
 * This is the check that does not go through the model. The implementation looks at what
 * the person actually typed — the run's own input, which the model cannot write — and
 * answers yes only if the agreement is in there, in their words. Everything the model says
 * about having asked is irrelevant to it.
 *
 * **What it is not.** It does not make an approved product text a formal approval record,
 * and it does not protect a run nobody is watching: with no human turn behind the run
 * there is no confirmation to find, and the answer is no. That is the intended behaviour —
 * a scheduled agent should not be cancelling batches — and it is why the gate is applied
 * at the tools that write, rather than at the agent.
 */
interface HumanConfirmationInterface
{
    /**
     * Whether this turn carries the person's agreement.
     */
    public function isGiven(): bool;

    /**
     * The answer a tool hands back instead of acting: what the model has to show the
     * person, and what the person has to write for the call to go through next time.
     *
     * @param string $summary what exactly would happen, in one sentence, already filled in
     *                        with the numbers — "schváliť všetkých 8 nerozhodnutých polí
     *                        produktu ABC-123"
     */
    public function ask(string $summary): string;
}
