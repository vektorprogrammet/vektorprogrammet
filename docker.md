# Bruk av Docker
Ved å bruke docker slipper du å installere programmer og sette opp systemet ditt
med riktige versjoner og konfigurasjoner av php og slik. Docker lar deg bygge et
*image*, som er et viritruelt filsystem der akkurat de riktige programmene er
installert. Dette bildet kan så startes som en *container*, og vektorprogrammets
server kan kjøres inni den containeren.

## Lag docker-bildet
I repoet ligger `/Dockerfile`, som beskriver hvilke kommandoer som skal
kjøres for å omdanne en fersk installasjon av `ubuntu 18.04` til en velsmurt
vektorprogrammet-utviklings-server.
```
npm run docker:dev:build
```
Dette vil starte en ny container, installere `php7.2`, `npm` osv, og kopiere
all vektorprogrammet-koden inn i det viritruelle filsystemet. Deretter kjøres
`npm run setup` inni containeren. Etter oppsettet er ferdig lagres hele
filsystemet til docker-bildet `vektordev:1.0`.

## Kjør docker-bildet
Nå kan du kjøre docker-bildet med kommandoen
```
docker run -it -p 8000:8000 vektordev:1.0
```
Denne komandoen vil starte en container fra bildet `vektordev:1.0`, og kjøre
kommandoen `npm start` inni. Instillingene `-it` gjør at du får kontroll over
terminalen inni containeren, og kan bruke Ctrl-C for å skru av serveren.
Instillingen `-p 8000:8000` gjør at nettverksporten `8000` blir tilgjengelig
også utenfor containeren, f.eks. i nettleseren.

Sjekk i terminalen at alt ser funksjonelt ut, og åpne siden i nettleseren din på
[localhost:8000].

## Gjør endringer
Hvis du nå gjør endringer i databasen
