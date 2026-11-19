<?php

declare(strict_types=1);

namespace Leoness\AgentSdk;

/**
 * Names of the platform tools a tool package may point the model to.
 *
 * The name is the contract: a package cannot see the platform's tool classes, so this
 * is the one place both sides read the same spelling from — the core tool classes
 * define their `NAME` off these constants, and a package's tool description that says
 * "the template goes to `vytvor_xlsx(sablona: ID)`" stays right when the platform renames.
 */
final class PlatformTools
{
    /** Reads an uploaded attachment (XLSX, PDF, e-mail) in pieces. */
    public const string ATTACHMENT_READ = 'precitaj_prilohu';

    /** Writes an XLSX, optionally over a template file given by its ID. */
    public const string SPREADSHEET_WRITE = 'vytvor_xlsx';

    /** Writes a document the model described, as a PDF. */
    public const string PDF_WRITE = 'vytvor_pdf';

    /** Writes a DOCX from the same description as {@see self::PDF_WRITE}. */
    public const string WORD_WRITE = 'vytvor_docx';

    /**
     * Writes a priced offer as a PDF, from data the platform prices itself.
     *
     * Told apart from {@see self::PDF_WRITE} because what it takes is different in kind:
     * not blocks, but the offer's data — quantities, units, prices with their sources.
     * Every figure in the document is computed by the platform, and the finished PDF is
     * read back and compared with what it was supposed to say.
     */
    public const string QUOTE_WRITE = 'vytvor_ponuku';
}
