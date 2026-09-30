<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * Names of the platform tools an agent package may whitelist in its manifest.
 *
 * The name is the contract: a package cannot see the platform's tool classes, so this
 * is the one place both sides read the same spelling from — the core tool classes
 * define their `NAME` off these constants. Whether a tool actually exists in the
 * deployment is still checked at installation time, where the registry is.
 *
 * The list grows as packages need it; a platform tool missing here can always be
 * whitelisted by its literal name at the price of a typo nobody catches in CI.
 */
final class PlatformTools
{
    /** Reads an uploaded attachment (XLSX, PDF, e-mail) in pieces. */
    public const string ATTACHMENT_READ = 'precitaj_prilohu';

    /** Writes an XLSX, optionally over a template file the agent version carries. */
    public const string SPREADSHEET_WRITE = 'vytvor_xlsx';
}
