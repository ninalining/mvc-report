# kmom03

## Flödesdiagram och pseudokod

Att modellera kortspelet med flödesdiagram och pseudokod hjälpte mig att strukturera min tankegång innan jag började koda. Flödesdiagrammet gav en tydlig överblick över spelets olika tillstånd och hur de hänger ihop – till exempel vad som händer när spelaren blir tjock eller när banken ska ta kort. Pseudokoden hjälpte mig att bryta ner logiken i mindre delar, som att beräkna handvärde och hantera ess som antingen 1 eller 14. Jag tror att denna typ av förberedelse stödjer problemlösningen, särskilt när man arbetar med tillståndsbaserade applikationer.

## Min implementation

Jag valde att implementera Kortspelet 21 där spelaren spelar mot banken. Jag skapade en Game21-klass som innehåller all spellogik – att dela kort, hantera spelarens val, bankens automatiska spel och jämförelse av poäng. Kontrollern är tunn och hanterar bara HTTP-förfrågningar, sessioner och rendering av templates.

Jag återanvände mina befintliga klasser Card, CardGraphic, CardHand och DeckOfCards genom komposition i Game21-klassen. Poängberäkningen följer kursens regler: J=11, Q=12, K=13 och Ess=1 eller 14.

Jag är ganska nöjd med resultatet. Spelet fungerar som det ska och koden är relativt enkel att följa. En förbättringsmöjlighet vore att lägga till möjligheten att satsa pengar eller att göra banken smartare. Jag skulle också kunna förbättra UI:t med bättre grafik för korten.

## Symfony

Jag känner mig mer och mer bekväm med Symfony. Routing, controllers och Twig fungerar bra tillsammans och det blir naturligt att följa MVC-mönstret. Sessionhanteringen var enkel att använda för att spara spelstatus mellan requests. Det som fortfarande kan vara utmanande är att hålla koll på alla konfigurationsfiler och att förstå hur ramverkets olika delar hänger ihop under ytan.

## TIL

Min TIL för detta kmom är vikten av att planera innan man kodar. Flödesdiagrammet och pseudokoden sparade mig tid under implementationen eftersom jag redan hade tänkt igenom logiken. Jag lärde mig också hur man installerar och konfigurerar statiska analysverktyg som PHPStan och PHPMD för att hålla kodkvaliteten hög.
