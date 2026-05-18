# kmom06

## Phpmetrics

Phpmetrics gav en bra överblick av kodens kvalitet. De mest intressanta mätvärdena var cyklomatisk komplexitet (CCN) och underhållbarhetsindex (MI). Mitt genomsnittliga MI-värde var 78.84 vilket är bra (över 65 anses acceptabelt). Controllers hade naturligt högre komplexitet eftersom de binder samman flera delar av applikationen. LibraryController stod ut med högst WMC (Weighted Methods per Class) på grund av att den hanterar CRUD-operationer och filuppladdning.

## Scrutinizer

Scrutinizer CI ger automatisk kodanalys vid varje push. Tjänsten analyserar kodkvalitet, hittar potentiella buggar och mäter testtäckning. Badges i README.md visar snabbt projektets status. Scrutinizer kompletterar phpmetrics genom att ge rekommendationer på specifika kodrader och en övergripande kvalitetspoäng.

## Förbättringar

Jag gjorde tre förbättringar baserat på phpmetrics-analysen:

1. **LibraryController – Extraherade handleImageUpload():** Bilduppladdningslogiken var duplicerad i createPost() och editPost(). Jag bröt ut den till en privat metod, vilket minskade duplicering och förbättrade underhållbarheten.

2. **Game21 – Extraherade cardPoints():** Match-uttrycket för att konvertera kortvärden till poäng låg inuti getHandValue()-loopen. Jag extraherade det till en separat privat metod, vilket gör koden mer läsbar och gör det enklare att testa värdeberäkningen isolerat.

3. **DeckOfCards – Förenklad getGroupedCards():** Metoden hade en if-sats med continue som skapade onödig komplexitet. Jag ersatte den med en ternär operator som gör logiken tydligare och mer koncis.

## Diskussion om "clean code"

Kan man aktivt jobba med kodkvalitet på detta sätt? Ja, absolut. Verktyg som phpmetrics och Scrutinizer ger objektiva mätvärden som hjälper till att identifiera problemområden. Det är dock viktigt att inte bli besatt av siffrorna – ibland är en metod med något högre komplexitet den mest läsbara lösningen. Verktygen är bäst som komplement till kodgranskning och goda designprinciper, inte som ersättning.

Andra sätt att jobba mot "clean code" inkluderar parprogrammering, kodgranskning (code review), att följa SOLID-principerna, och regelbunden refaktorering. Det viktigaste är att ha en kultur där kodkvalitet värderas och att verktygen stödjer det arbetet.

## TIL

Min TIL för detta kmom är hur man använder automatiserade verktyg för att mäta och förbättra kodkvalitet. Phpmetrics visade konkret vilka klasser som behöver uppmärksamhet, och att bryta ut duplicerad kod till privata metoder är en enkel men effektiv förbättring. Jag lärde mig också att konfigurera CI-tjänster som Scrutinizer för kontinuerlig kodanalys.