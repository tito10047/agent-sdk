# leoness/agent-sdk

Kontrakty medzi platformou Leoness AI a agentovými balíkmi. Agentové repozitáre
závisia **iba** od tohto balíka (a v dev od `leoness/agent-testkit`) — kód core
aplikácie nikdy nevidia.

## Čo tu je

- `AgentInterface` + `Dto\AgentManifest` — celý kontrakt kódového agenta; trieda
  s tagom `AgentInterface::TAG` je agentom od najbližšej synchronizácie.
- `DeclarativeBlueprintInterface` — agent, ktorý by sa dal naklikať v administrácii,
  ale balík ho dodáva v kóde, aby ho čerstvé nasadenie malo funkčný. Iba *zasieva*:
  po publikovaní je to obyčajný deklaratívny agent, ktorý smie operátor prevziať.
- `HandlesFinishedRun` + `FinishedRunInterface` + `RunFileView` — reakcia agenta
  na vlastný dokončený beh, cez read-only pohľad namiesto entity.
- `DataSource\DataSourceInterface` + `DataSourceManifest` — dátové zdroje.
- `Platform\RecipientDirectoryInterface` — úzky port na e-maily administrátorov.
- `Platform\RunObserverInterface` — „beh môjho agenta začal / skončil“; jediné dvere
  do frontovej udalosti platformy. Volá sa pre **každý** beh, takže prvé, čo
  implementácia urobí, je porovnanie `agentCode()` s vlastným kódom.
- `Platform\RunDispatchInterface` + `Dto\RunRequestSpec` / `Dto\DispatchedBatch` —
  fronta. Nemá `requestedBy`: kto sa pýta, dopĺňa platforma z aktuálneho tokenu.
- `Platform\AgentDirectoryInterface` + `Dto\RunnableAgentVersion` — čo prijíma tá
  verzia agenta, ktorá by práve teraz bežala.
- `Platform\AgentAuthorizationInterface` — „smie tento človek spustiť tohto agenta“;
  odpovedá ten istý voter ako zvyšku platformy. Default deny, nikdy výnimka.
- `Platform\Entity\AgentRunInterface` a `Platform\Entity\PlatformUserInterface` —
  read-only pohľad na beh a na konto, a zároveň Doctrine cieľ pre `targetEntity:`
  vlastnej entity balíka (platforma ich preloží cez `resolve_target_entities`, FK
  aj `ON DELETE` zostávajú skutočné).
- `Exception\*` — `UnknownAgentException`, `NoPublishedVersionException`,
  `InvalidAgentInputException`, `EmptyBatchException`, `FileUnreadableException`.
  Výnimky platformy ich extendujú, takže `catch` napísaný proti kontraktu chytí
  skutočnú. `UnknownCriterionException` je opačný smer: hádže ju *balík* zo svojho
  `DataSourceInterface::fetch()`, keď dostane kritérium, ktoré nedeklaroval.
- `Tool\ToolAnswer` — ako nástroj odpovedá modelu (JSON s prítomnými prázdnymi
  kľúčmi, bežné „nenašlo sa“ ako veta, nie výnimka).
- `Tool\HumanConfirmationInterface` — potvrdenie, ktoré nejde cez model.
- `Prompt\UntrustedData` — ohradenie dát, ktoré platforma nepísala, aby model videl,
  kde začínajú a končia.
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

## Verzia 2.0

Čisto aditívna oproti 1.1 — nič sa neodobralo. Major preto, že sa ňou SDK rozšírilo
z „kontrakty agenta“ na „porty platformy“ (fronta, register, oprávnenia, entity), a
balíky majú explicitne povedať, že s touto plochou počítajú. Administračné kontrakty
(menu, odkazy do EasyAdminu) sú v samostatnom `leoness/agent-sdk-admin` — SDK nesmie
ťahať EasyAdmin.
