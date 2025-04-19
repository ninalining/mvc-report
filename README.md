![Banner](https://github.com/ninalining/mvc-report/raw/main/public/img/databas.jpg)

# Symfony MVC-webbplats – Nina Li

Detta är mitt projekt i MVC-kursen på BTH, byggt med Symfony. Projektet visar en grundläggande implementering av MVC-arkitekturen, inklusive routing, templating, SCSS-stilhantering och JSON API-gränssnitt.

---

## Introduktion

Detta projekt syftar till att visa följande:

- Grundläggande struktur av Symfony
- Användning av Twig-mallmotorn
- Webpack Encore för hantering av statiska resurser
- Skapa modulära stilar med SCSS
- Implementera JSON API-gränssnitt och returnera formaterade svar
- Responsiv och tydlig layoutstil

---

## Webbplatsens funktioner

- `/`：Projektintroduktion och personlig profil (använder bilder och SCSS-layout)
- `/about`：Introduktion till kursen mvc och projektets källkod, länk till GitHub
- `/report`：Sammanfattningsrapport för varje kursmodul (kmom) med möjlighet till snabb navigering via ankarpunkter
- `/lucky`：Generera ett dynamiskt lyckonummer med roterande bilder och lekfull stil
- `/api/quote`：Returnera ett slumpmässigt citat i JSON-format, inklusive datum och tidsstämpel
- `/api`：Visa aktuella alla JSON API-rutter

---

## Installation & Startmetod

Se till att du har installerat `composer`, `npm` och `symfony-cli`.

```bash
git clone https://github.com/ninalining/mvc-report.git
cd mvc-report
composer install
npm install
npm run build
symfony server:start