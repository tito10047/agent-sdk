# leoness/agent-sdk

Kontrakty medzi platformou Leoness AI a agentovými balíkmi. Agentové repozitáre
závisia **iba** od tohto balíka (a v dev od `leoness/agent-testkit`) — kód core
aplikácie nikdy nevidia.

## Čo tu je

- `AgentInterface` + `Dto\AgentManifest` — celý kontrakt kódového agenta; trieda
  s tagom `AgentInterface::TAG` je agentom od najbližšej synchronizácie.
- `HandlesFinishedRun` + `FinishedRunInterface` + `RunFileView` — reakcia agenta
  na vlastný dokončený beh, cez read-only pohľad namiesto entity.
- `DataSource\DataSourceInterface` + `DataSourceManifest` — dátové zdroje.
- `Platform\RecipientDirectoryInterface` — úzky port na e-maily administrátorov.
- Enumy zdieľané s platformou: `RunStatus`, `RunTrigger`, `ModelMode`, `Role`,
  `AgentInputType`, `OutputFieldKind`, `FactStatus`, `ValidationVerdict`
  (string hodnoty sú uložené v DB platformy — nemeniť bez major verzie).
- `Dto\FactClaim` — konvencia „hodnota + stav + zdroj“ pre údaje, ktoré si model
  nesmie vymyslieť (zadanie §11).
- `Validation\*` — to isté vynucovanie manifestu, aké robí core pri inštalácii:
  `OutputSchemaRules` (strict structured output), `ManifestLinter`,
  `TechnicalFieldLint`, `RuleExpressionLanguage` + `RuleFunctions`.

## Pravidlá pre agentové balíky

- Type-hintuj výhradne `Leoness\AgentSdk\*`, `Psr\*`, `Symfony\*` — nikdy `App\*`.
- Manifest je čistý popis: žiadne query, žiadne hodiny, žiadna náhoda.
- Semver: zmena signatúry interfaceu alebo odobratie enum hodnoty = major;
  nový konštruktorový parameter s defaultom = minor.
