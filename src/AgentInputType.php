<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The kind of value an agent input field carries.
 *
 * Deliberately small: it drives which widget the agent builder renders and how the
 * value is checked before it reaches the prompt. Anything richer belongs in a data
 * source, not in a hand-filled input.
 */
enum AgentInputType: string implements TranslatableInterface
{
    case Text = 'text';
    case LongText = 'long_text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Url = 'url';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::LongText => 'Dlhý text',
            self::Number => 'Číslo',
            self::Boolean => 'Áno / nie',
            self::Url => 'URL',
        };
    }
}
