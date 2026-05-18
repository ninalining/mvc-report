# kmom04

## Att skriva enhetstester med PHPUnit

Att skriva kod som testar annan kod kändes naturligt och givande. PHPUnit har en tydlig struktur med Arrange-Act-Assert som gör det enkelt att organisera sina tester. Jag uppskattade särskilt hur snabbt man får feedback – alla 41 tester körs på under en sekund. Det som var mest utmanande var att testa klasser med slumpmässigt beteende, som Game21 där kortleken blandas. Jag löste det genom att testa logik som inte beror på slumpen separat, till exempel handvärdesberäkningen med specifika kort.

Generellt tycker jag att PHPUnit känns som ett moget och väldokumenterat verktyg. Det var lätt att komma igång och integrationen med Xdebug för kodtäckning fungerade smidigt.

## Kodtäckning

Jag lyckades nå 93% kodtäckning på radnivå, vilket överstiger målet på 90%. Alla fyra Card-klasser har 100% täckning. Game21 ligger på cirka 97% – de enda raderna som inte täcks helt är vissa edge cases i dealerns spellogik som kräver specifika kortordningar. Jag valde att inte tvinga fram dessa scenarion med mocking eftersom det skulle göra testerna svårare att underhålla.

## Testbar kod

Min kod visade sig vara relativt testbar. De rena modellklasserna (Card, CardHand, DeckOfCards) var mycket enkla att testa eftersom de inte har externa beroenden. Game21 var lite svårare att testa på grund av att den skapar sina egna beroenden internt (new DeckOfCards). Om jag hade använt dependency injection istället hade jag kunnat mocka kortleken och testa specifika scenarion mer deterministiskt.

Controller-klasserna är medvetet exkluderade från testerna eftersom de är tunna och mest hanterar HTTP-logik. De hade krävt integrationstester snarare än enhetstester.

## Kodförbättringar

Jag valde att inte skriva om koden i detta moment. Min befintliga struktur med tunna controllers och ren spellogik i Game21 fungerade bra för testning. Jag la dock till PHPDoc-kommentarer på Game21-klassen för att förbättra dokumentationen och göra koden mer lättförståelig för andra utvecklare.

## Testbar kod och "snygg kod"

Jag anser att testbar kod ofta sammanfaller med snygg och ren kod. Kod som är lätt att testa tenderar att följa principer som Single Responsibility och låg koppling – samma principer som gör kod läsbar och underhållbar. Om en metod är svår att testa beror det ofta på att den gör för mycket eller har för många beroenden, vilket även gör den svårare att förstå och underhålla.

## TIL

Min TIL för detta kmom är hur kodtäckning kan användas som ett verktyg för att identifiera oprövade kodstigar och hur det uppmuntrar en att tänka igenom edge cases. Jag lärde mig också att verktyg som phpDocumentor kan generera professionell API-dokumentation automatiskt från PHPDoc-kommentarer.