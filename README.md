# Damir CRM

Jednostavan CRM sustav izrađen u Laravelu, prema zadanoj specifikaciji

## Tehnologije
- Laravel 13, PHP 8.4
- MySQL
- Blade + Tailwind CSS (Vite)
- Laravel Breeze (autentifikacija)
- barryvdh/laravel-dompdf (PDF export)

## Funkcionalnosti
- Prijava (login)
- Entiteti: Tvrtke, Kontakti, Dealovi, Ponude — CRUD za svaki
- Kanban prikaz (po statusu/fazi) za svaki entitet
- Shortcut za izradu ponude direktno iz Deal Kanban prikaza (automatski prebacuje status deala u "ponuda poslana")
- Export ponude u PDF
- Dashboard s pregledom ključnih metrika (broj tvrtki, kontakata, dealova, ponuda, korisnika, vrijednost otvorenih/dobivenih dealova)
- Soft delete za sve entitete (obrisani zapisi se ne prikazuju, ali ostaju u bazi)

## Poslovne odluke
- **Kanban je implementiran za sve entitete** (Tvrtke, Kontakti, Dealovi, Ponude), ne samo za Dealove — specifikacija eksplicitno traži Kanban "za svaki entitet".
- **Deal može imati više Ponuda** (1:N) radi mogućnosti revizije ponude (npr. v1, v2 nakon pregovora).
- **Kontakt je opcionalan na Dealu** — Deal je uvijek vezan uz Tvrtku, kontakt nije obavezan.
- **Status update na Kanbanu** rađen kroz dropdown + form submit umjesto drag-and-drop — HTML5 drag-and-drop je nepouzdan na dodirnim uređajima, dropdown radi konzistentno na svim platformama.
- **Model korištenja: single-tenant interni CRM.** Aplikaciju koristi jedna firma (vlasnik CRM-a) s više zaposlenika (korisnika). Entiteti "Tvrtke" i "Kontakti" predstavljaju klijente/kupce te firme, ne odvojene korisnike sustava — svi zaposlenici ih dijele i vide zajedno, jer je to standardna logika CRM-a.
- **Vlasništvo nad Dealovima i Ponudama**: svaki Deal i Ponuda vezani su uz korisnika koji ih je kreirao (`user_id`), i samo taj korisnik smije uređivati/brisati/mijenjati status. Tvrtke i Kontakti ostaju zajednički vidljivi i uredivi svim korisnicima, jer predstavljaju zajedničku bazu klijenata firme. Ovo sprječava da jedan zaposlenik nenamjerno izmijeni ili obriše tuđi posao/ponudu.
- **Registracija i mail nisu dio spec-a** (spec traži samo Login). `MAIL_MAILER` je namjerno postavljen na `log` — email potvrde/reset lozinke se ne šalju stvarno, nego zapisuju u log. Novi korisnici (zaposlenici) se dodaju ručno (npr. preko `php artisan tinker` ili buduće admin funkcije), a ne kroz javnu registraciju, jer to bolje odgovara internom alatu.

## Pokretanje projekta lokalno
1. `git clone https://github.com/PeeroZg/damir-crm.git`
2. `cd damir-crm`
3. `composer install`
4. `cp .env.example .env` — promijeni `DB_CONNECTION` na `mysql` (default je `sqlite`) i popuni `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST`, `DB_PORT`
5. `php artisan key:generate`
6. `php artisan migrate`
7. `npm install && npm run build`
8. `php artisan serve`

## Online
- URL: https://damircrm.tennisuper.com
- Repozitorij: https://github.com/PeeroZg/damir-crm
- Podaci za prijavu: dostavljeni zasebno (vidi popratni email)
