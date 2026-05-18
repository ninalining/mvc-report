# kmom05

## Symfony och Doctrine

Det gick bra att jobba igenom övningen med Doctrine. Konfigurationen med SQLite var enkel – man behöver bara ändra DATABASE_URL i .env-filen och sedan fungerar det direkt. Det som jag reagerade på var hur smidigt det är att skapa entiteter med PHP-attribut istället för XML eller YAML. Migrations-systemet kändes också genomtänkt – man genererar en migration baserat på skillnaden mellan sin kod och databasens nuvarande tillstånd, och sedan kan man köra den framåt eller bakåt.

## Min applikation

Jag byggde en "Bibliotek"-applikation där man kan hantera böcker. Varje bok har en titel, ett ISBN-nummer, en författare och en bild. Jag tänkte på att användargränssnittet skulle vara enkelt och intuitivt – man ser alla böcker i en tabell, kan klicka på en bok för att se detaljer, och därifrån kan man redigera eller ta bort boken. Formulären för att skapa och redigera böcker är enkla med tydliga fältnamn. Jag la också till en "Återställ biblioteket"-funktion som fyller databasen med tre standardböcker, vilket gör det enkelt att testa.

## ORM och CRUD

Det gick bra att jobba med ORM i CRUD. Jämfört med att skriva ren SQL med PDO (som i databas-kursen) så slipper man tänka på SQL-syntax och kan istället fokusera på objekten. Att spara en bok blir så enkelt som `$em->persist($book); $em->flush();`. Nackdelen är att man tappar lite kontroll och transparens – det är svårare att se exakt vilka SQL-frågor som körs i bakgrunden. För enklare applikationer som denna fungerar ORM utmärkt, men för mer komplexa databasoperationer kan det vara bättre att ha mer kontroll med ren SQL.

## Uppfattning om ORM

ORM känns som ett bra verktyg för de flesta webbapplikationer. Det minskar mängden boilerplate-kod och gör det lättare att underhålla databasrelaterad kod. Repository-mönstret som Doctrine använder gör det också lätt att organisera sina databasfrågor. Jag uppskattar att man kan byta databas (till exempel från SQLite till MySQL) utan att ändra sin applikationskod – det är en stor fördel med abstraktionslagret. Däremot kan ORM vara överkill för väldigt enkla applikationer eller situationer där prestanda är kritisk.

## TIL

Min TIL för detta kmom är hur Doctrine ORM abstraherar bort databasen och låter mig jobba med vanliga PHP-objekt istället för SQL. Jag lärde mig också hur migrations fungerar för att versionhantera databasförändringar, vilket är viktigt i teamarbete och vid deployment.