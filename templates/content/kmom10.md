# kmom10

## Krav 1–3: Webbplats

Jag valde att bygga ett Black Jack-kortspel som mitt projekt. Landningssidan finns på `/proj` och är länkad från report-sidans navbar. Projektet har en helt egen stil med mörkt casino-tema i mörkblå och guld (#1a1a2e, #e2b714), egna typsnitt (Playfair Display och Roboto) samt en separat navbar med länkarna Hem, Spela, Om, API och Statistik. Designen skiljer sig tydligt från report-sidan med en helt ny SCSS-fil (`proj.scss`).

Spelet följer riktiga Black Jack-regler: spelaren kan spela 1–3 händer samtidigt, ess räknas som 1 eller 11 automatiskt, klädda kort ger 10 poäng, och banken stannar på 17 eller högre. Black Jack (ess + 10-värdekort) ger 3:2 i vinst. Spelaren loggar in med sitt namn, väljer insats och antal händer, och spelar sedan mot banken med hit/stand. Sidan `/proj/about` beskriver projektet, spelregler och teknikval.

Dokumentationen i `docs/` är uppdaterad med phpdoc-genererad API-dokumentation och phpmetrics-rapport. Enhetstester täcker över 90% av spelklasserna (BlackJackHand 100%, BlackJackAI 100%, Player 100%, GameRound 100%, BlackJack ~88%). README.md finns men saknar ännu Scrutinizer-badges.

## Krav 4: JSON API

Jag skapade ett JSON API med fem routes i `ApiBlackJackController`. API:t använder en separat sessionsnyckel (`bj_api_game`) för att inte störa det vanliga spelet. De fem routerna är: `POST /proj/api/game/start` (starta nytt spel med namn, insats och antal händer), `POST /proj/api/game/hit` (ta ett kort), `POST /proj/api/game/stand` (stanna), `GET /proj/api/game/status` (hämta spelstatus) och `GET /proj/api/player/{name}/stats` (hämta spelarstatistik från databasen).

Sidan `/proj/api` presenterar alla API-routes med beskrivningar och testformulär. Man kan klicka på knapparna direkt på sidan för att testa varje endpoint och se JSON-svaret. POST-routerna har formulär med input-fält och GET-routerna har direktlänkar.

## Krav 5: ORM

Projektet använder Doctrine ORM med SQLite som databas. Databasen innehåller två tabeller: `player` (sparar spelarens namn och saldo) och `game_round` (sparar varje spelrunda med insats, resultat, spelarens poäng, bankens poäng och tidsstämpel). Tabellerna har en ManyToOne/OneToMany-relation där varje GameRound tillhör en Player.

Sidan `/proj/about/database` innehåller ett ER-diagram som visar relationen, beskrivningar av tabellerna och en diskussion om ORM kontra traditionellt databasarbete. Jag valde SQLite för enkelhetens skull – det kräver ingen extern databasserver och databasen finns som en fil i `var/data.db`.

Fördelen med ORM jämfört med det sätt vi jobbade i databaskursen är att man slipper skriva rå SQL och istället arbetar med PHP-objekt. Doctrine mappar automatiskt mellan entiteter och tabeller, hanterar relationer med annotations/attributes, och migrationer gör det enkelt att versionskontrollera databasändringar. Nackdelen är att ORM lägger till ett abstraktionslager som kan göra det svårare att optimera komplexa frågor. I databaskursen hade man full kontroll över SQL-frågorna, vilket var bra för att lära sig hur databaser fungerar på djupet. ORM är dock mer produktivt för typiska CRUD-operationer. Jag tycker att båda sätten har sitt värde – ORM för snabb utveckling och raw SQL för komplexa frågor och djupare förståelse.

Enhetstester för entiteterna (Player och GameRound) har 100% kodtäckning. Testerna verifierar att getters/setters fungerar korrekt och att relationen mellan Player och GameRound hanteras rätt.

## Krav 6: Avancerade features

Jag implementerade tre avancerade features utöver baskraven:

**1. Split (dela par):** Spelaren kan splitta en hand om de två första korten har samma värde. Då delas handen i två separata händer med varsin insats, och varje hand får ett nytt kort. Spelaren spelar sedan varje hand individuellt. Detta krävde betydande omstrukturering av spellogiken för att hantera multipla aktiva händer, dynamisk arrayhantering med `array_splice`, och korrekt indexering när man navigerar mellan händerna. Logiken för att avgöra vilken hand som är aktiv och automatiskt gå vidare till nästa hand var utmanande att implementera korrekt.

**2. AI-datorspelare:** En AI-spelare kan aktiveras via en kryssruta på insatssidan. AI:n spelar parallellt med spelaren och använder en grundläggande strategitabell baserad på riktig Black Jack basic strategy. Klassen `BlackJackAI` analyserar sin hand mot bankens synliga kort och beslutar om hit eller stand. AI:n stannar på 17+, tar kort på ≤8, och vid 12–16 beror beslutet på bankens kort (stand mot 2–6, hit mot 7–11). AI:ns resultat visas separat i spelvyn. Det var en utmaning att integrera AI:ns spellogik i det befintliga spelflödet utan att störa spelarens interaktion.

**3. Statistikvisualisering:** Sidan `/proj/stats` visar detaljerad spelstatistik för alla spelare. Den hämtar data från databasen och presenterar vinstprocent med en visuell progress bar, en resultatfördelningsstapel (vinst/lika/förlust i olika färger) och en tabell med de senaste 10 rundorna. Statistiken ger spelaren insikt i sitt spelande och gör databasen meningsfull genom att visualisera den sparade datan.

## Projektet

Projektet gick bra att genomföra. Den största utmaningen var att hantera spelflödet med multipla händer, split och AI-spelare samtidigt – det blev snabbt komplext med indexhantering och tillståndsmaskin. Att hålla koll på vilken hand som är aktiv och när banken ska spela krävde noggrann planering. Symfony-ramverket och Doctrine ORM fungerade smidigt och sparade mycket tid jämfört med att bygga allt från grunden. Twig-templates och SCSS med Webpack Encore gjorde det enkelt att skapa en visuellt tilltalande sida. Enhetstesterna hjälpte till att fånga buggar tidigt, särskilt i spellogiken. Projektet tog ungefär 30 timmar att genomföra, och jag tycker det var ett bra och rimligt projekt för kursen – lagom utmanande utan att vara överväldigande.

## Kursen

Kursen MVC har gett mig en bra grund i objektorienterad PHP-utveckling med Symfony-ramverket. Det var värdefullt att lära sig om design patterns, enhetstestning, kodkvalitetsverktyg och ORM. Materialet var genomgående bra och steg-för-steg-upplägget gjorde det hanterbart att lära sig ramverket. Övningarna i kmom01–06 byggde upp kunskapen gradvis inför projektet, vilket var uppskattat. Om jag skulle föreslå en förbättring vore det att inkludera mer om Doctrine-relationer och avancerad ORM-användning tidigt i kursen, så att man är bättre förberedd inför projektet. Överlag är jag nöjd med kursen och skulle rekommendera den till andra. Betyg: 8/10.
