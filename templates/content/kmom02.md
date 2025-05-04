# kmom02

I PHP används flera objektorienterade konstruktioner för att strukturera och återanvända kod:

Arv: Arv används för att skapa en hierarki mellan klasser där en subklass kan ärva egenskaper och metoder från en basklass. I detta kmom använder jag arv genom att CardController ärver från AbstractController, som är en del av Symfony. Det gör att jag kan använda metoder som render() och addFlash() direkt i min controller utan att definiera dem själv.

Komposition: Komposition innebär att en klass innehåller instanser av andra klasser som dess egenskaper. I detta kmom använder jag komposition i både DeckOfCards och CardHand. Dessa klasser innehåller flera Card-objekt i en array. Till exempel innehåller DeckOfCards en hel kortlek genom att skapa 52 olika Card-objekt och lagra dem i $deck. På samma sätt använder CardHand en lista av Card för att representera en spelares hand. Det gör det enkelt att hantera flera kort som en grupp.

Interface: Interface används för att definiera en uppsättning metoder som en klass måste implementera. I detta kmom använde jag inte egna interface, men jag har använt Symfony:s inbyggda SessionInterface i mina controllers. Det innebär att jag arbetar mot ett kontrakt snarare än en specifik implementation.

Trait: Traits används för att dela kod mellan klasser utan att använda arv. Jag har inte skapat ett eget trait i detta moment, men jag tänkte möjligheten att extrahera flash-meddelanden till ett FlashMessageTrait. Det skulle kunna förenkla hanteringen av addFlash() i flera controllers. Det gav mig bättre förståelse för när traits kan vara användbara – till exempel när man vill återanvända logik utan att skapa en bas-klass.

## **Min implementation**

Jag började med att skapa en struktur för projektet enligt Symfony och MVC-arkitekturen. För kortleksdelen implementerade jag klasserna Card, CardHand och DeckOfCards. Dessa klasser hanterar kortens egenskaper, en samling kort och hela kortleken. Jag använde metoder som shuffle, drawCard och sort för att hantera kortleken.

Jag är nöjd med hur jag lyckades strukturera koden och använda objektorienterade principer. 

## **Reflektioner kring Symfony och MVC**

Att arbeta med Symfony har varit en lärorik upplevelse. Jag uppskattar hur ramverket hanterar routing, templating och sessioner på ett strukturerat sätt. Det var särskilt intressant att använda Twig för att skapa återanvändbara mallar och Webpack Encore för att hantera SCSS och JavaScript.

Jag märkte dock att det kan vara en utmaning att hålla koden ren och organiserad när projektet växer. Det är viktigt att följa principerna för separation av ansvar och att använda tjänster och dependency injection för att undvika att controllers blir för stora.

## **TIL för kmom02**

Min TIL för detta kmom är hur man kan använda Symfony för att bygga en webbapplikation enligt MVC. Jag lärde mig också mer om hur man hanterar sessioner och använder objektorienterade principer i PHP. Dessutom fick jag en bättre förståelse för hur man kan använda SCSS för att skapa modulära och återanvändbara stilar.

## **Förbättringar baserat på feedback**

Utifrån feedback från läraren har jag lagt till /api i navigationsmenyn för att underlätta för rättare. Jag har också fokuserat på att förbättra strukturen och tydligheten i min redovisningstext, samt säkerställt att mitt Git-repo är välorganiserat med tydliga commits och taggar.