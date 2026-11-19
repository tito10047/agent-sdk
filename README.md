# leoness/agent-sdk

Kontrakty medzi platformou Leoness AI a **balíkmi MCP nástrojov**. Balík závisí **iba**
od tohto balíka (a v dev od `leoness/agent-testkit`) — kód core aplikácie nikdy nevidí.
Platforma nemá vlastných agentov ani prompty; balík pridáva nástroje (`#[AsTool]`), ktoré
volá Claude alebo ChatGPT cez MCP v mene prihláseného používateľa.

## Čo tu je

- `Platform\AgentFileAccessInterface` + `Platform\StoredFileHandleInterface` — súbor
  podľa ID, ktoré používateľ dostal z `priprav_nahratie` alebo z odpovede iného nástroja,
  vždy v rozsahu toho, čo smie čítať. Súbory sa v argumentoch nikdy neposielajú.
- `Platform\Entity\PlatformUserInterface` — read-only pohľad na konto a Doctrine cieľ
  pre `targetEntity:` vlastnej entity balíka (platforma ho preloží cez
  `resolve_target_entities`).
- `Confirmation\ConfirmationRequestsInterface` + `Dto\ProposedAction` — akcia, ktorá
  čaká na človeka: nástroj ju opíše (typ, veta so všetkými číslami, payload) a vráti
  číslo potvrdenia a podpísaný odkaz (`Dto\IssuedConfirmation`); stav sa pýta nástrojom
  platformy `stav_potvrdenia`.
- `Confirmation\ConfirmedActionExecutorInterface` + `Dto\ActionOutcome` — vykonávateľ
  jedného typu akcie; platforma ho zavolá po potvrdení, pod identitou toho, kto akciu
  navrhol.
- `Catalogue\CatalogueItemsInterface` — port, cez ktorý platforma zisťuje údaje
  o produktoch; adaptér dodáva balík, ktorý dáta má.
- `Tool\ToolAnswer` — ako nástroj odpovedá modelu (JSON s prítomnými prázdnymi kľúčmi).
- `Tool\ToolRefusedException` — odmietnutie, s ktorým človek niečo urobí (chýba právo,
  súbor nie je jeho, záznam neexistuje). MCP server ho vráti ako výsledok s `isError: true`;
  tú istú triedu používajú nástroje platformy.
- `Prompt\UntrustedData` — ohradenie dát, ktoré platforma nepísala, aby model videl,
  kde začínajú a končia.
- `Dto\FactClaim` + `FactStatus` + `Validation\ClaimRules` — konvencia „hodnota + stav
  + zdroj“ pre údaje, ktoré si model nesmie vymyslieť (zadanie §11).
- `Dto\FileVerdict` — výsledok kontroly vytvoreného súboru.
- `PlatformTools` — názvy nástrojov platformy, na ktoré smie popis nástroja balíka
  odkázať; `Role` — role, ktoré smie balík kontrolovať.
- `Exception\FileUnreadableException`, `Support\CanonicalJson`.

## Pravidlá pre balíky nástrojov

- Type-hintuj výhradne `Leoness\AgentSdk\*`, `Psr\*`, `Symfony\*` — nikdy `App\*`.
- Každý nástroj má `#[IsGrantedTool('TOOL_<NÁZOV>')]` — oprávnenie je `TOOL_` a názov
  nástroja veľkými písmenami. Previerka balíka bez toho balík neprijme.
- Akcia, ktorú model nesmie spraviť sám (hromadný zápis, import), ide cez
  `ConfirmationRequestsInterface`, nie cez vetu v popise nástroja.
- Semver: zmena signatúry interfaceu alebo odobratie enum hodnoty = major;
  nový konštruktorový parameter s defaultom = minor.

## Verzia 4.1

Pribudli `Tool\ToolRefusedException`, `Platform\FileDeliveryInterface` (súbor, ktorý nástroj
vyrobil, ako `StoredFile` s podpísaným odkazom) a `Platform\ModelTimeInterface` (čas pre model
v ISO 8601 s posunom `Europe/Bratislava`). SDK kvôli výnimke závisí od `symfony/ai-agent`.

## Verzia 4.0

Z SDK zmizli agentové kontrakty — agent, manifest, výstupná schéma, dátové zdroje,
behy, fronta, register agentov a `HumanConfirmationInterface`;
`AgentFileAccessInterface` stratil `findRunOutput()` a `findRunInput()`. Pribudol
kontrakt potvrdenia akcie (`Confirmation\*`). Administračné kontrakty (menu, odkazy do
EasyAdminu) sú ďalej v `leoness/agent-sdk-admin`.
