-- Wealth Creators — voorbeelddata
-- Importeer dit NA schema.sql, zodat de pagina's tijdens het bouwen inhoud tonen.
-- De afbeelding-URL's zijn placeholders (picsum.photos) en worden later vervangen
-- door echte luxefoto's in assets/img/.

USE `wealth_creators`;

-- Demo-gebruiker (wachtwoord: Welkom123!)
-- Hash gegenereerd met password_hash('Welkom123!', PASSWORD_DEFAULT).
INSERT INTO `gebruikers` (`naam`, `email`, `wachtwoord_hash`) VALUES
('James Sterling', 'james@example.com', '$2y$12$Q2eYHGtkc1UtH4bLkJ0dN.9BIbOkr0rUQjFh7H9i5yJxLgmJAyb6C');

-- Categorieën
INSERT INTO `categorieen` (`naam`, `beschrijving`, `afbeelding_url`) VALUES
('Supercars',   'Zeldzame hypercars en limited editions.',          'https://picsum.photos/seed/supercars/800/600'),
('Jachten',     'Superjachten voor de open zee.',                   'https://picsum.photos/seed/jachten/800/600'),
('Helikopters', 'Privéhelikopters voor snelle verplaatsingen.',     'https://picsum.photos/seed/helikopters/800/600'),
('Horloges',    'Exclusieve uurwerken en haute horlogerie.',        'https://picsum.photos/seed/horloges/800/600'),
('Juwelen',     'Unieke diamanten en kostbare juwelen.',            'https://picsum.photos/seed/juwelen/800/600');

-- Producten (categorie_id verwijst naar de volgorde hierboven: 1=Supercars ... 5=Juwelen)
INSERT INTO `producten`
    (`categorie_id`, `naam`, `beschrijving`, `prijs`, `minimaal_bod`, `locatie`, `status`, `eigenaar_naam`) VALUES
(1, 'Bugatti La Voiture Noire',
    'Een uniek exemplaar, met de hand gebouwd. Het toppunt van Frans vakmanschap.',
    11000000.00, 9500000.00, 'Monaco',        'beschikbaar',  'Henri de Vries'),
(1, 'Pagani Huayra Roadster BC',
    'Slechts 40 exemplaren wereldwijd. Koolstofvezel en kunst in één.',
    3800000.00,  3200000.00, 'Milaan',        'beschikbaar',  'Lucia Conti'),
(2, 'Sunreef 80 Eco',
    'Volledig elektrisch superjacht met zonnepanelen geïntegreerd in de romp.',
    9500000.00,  8000000.00, 'Saint-Tropez',  'beschikbaar',  'Alexander Petrov'),
(2, 'Azimut Grande 36M',
    'Italiaans design, vier dekken en een eigenaarssuite op het hoofddek.',
    14500000.00, 12000000.00,'Porto Cervo',   'gereserveerd', 'Bianca Rossi'),
(3, 'Airbus ACH160',
    'Luxe privéhelikopter, fluisterstil en met een interieur op maat.',
    16000000.00, 14000000.00,'Genève',        'beschikbaar',  'Klaus Bergmann'),
(4, 'Patek Philippe Grandmaster Chime',
    'Het meest gecompliceerde polshorloge van Patek Philippe. Dubbelzijdig.',
    2700000.00,  2400000.00, 'Genève',        'beschikbaar',  'Sophie Laurent'),
(5, 'The Blue Heart Diamond',
    'Een zeldzame blauwe diamant van 12 karaat, gezet in platina.',
    23000000.00, 20000000.00,'Antwerpen',     'beschikbaar',  'Mei Tanaka');

-- Productafbeeldingen (product_id verwijst naar de volgorde hierboven: 1 ... 7)
INSERT INTO `product_afbeeldingen`
    (`product_id`, `afbeelding_url`, `is_hoofdafbeelding`, `volgorde`) VALUES
(1, 'https://picsum.photos/seed/bugatti1/1200/800', 1, 0),
(1, 'https://picsum.photos/seed/bugatti2/1200/800', 0, 1),
(1, 'https://picsum.photos/seed/bugatti3/1200/800', 0, 2),
(2, 'https://picsum.photos/seed/pagani1/1200/800',  1, 0),
(2, 'https://picsum.photos/seed/pagani2/1200/800',  0, 1),
(3, 'https://picsum.photos/seed/sunreef1/1200/800', 1, 0),
(3, 'https://picsum.photos/seed/sunreef2/1200/800', 0, 1),
(4, 'https://picsum.photos/seed/azimut1/1200/800',  1, 0),
(5, 'https://picsum.photos/seed/airbus1/1200/800',  1, 0),
(6, 'https://picsum.photos/seed/patek1/1200/800',   1, 0),
(7, 'https://picsum.photos/seed/diamond1/1200/800', 1, 0);

-- Voorbeeldbiedingen (zodat berichten.php meteen iets toont)
-- James (gebruiker_id=1) heeft twee biedingen; Olivia is een anonieme bezoeker (NULL).
INSERT INTO `biedingen`
    (`product_id`, `gebruiker_id`, `naam`, `email`, `bod_bedrag`, `bericht`, `status`) VALUES
(1, 1, 'James Sterling', 'james@example.com', 9800000.00,
    'Ik ben zeer geïnteresseerd in dit unieke exemplaar. Graag contact.', 'nieuw'),
(6, 1, 'James Sterling', 'james@example.com', 2500000.00,
    'Prachtig uurwerk. Is een bezichtiging in Genève mogelijk?', 'gelezen'),
(3, NULL, 'Olivia Chen', 'olivia@example.com', 8200000.00,
    'Het elektrische aspect spreekt mij erg aan. Ik wil graag een bod doen.', 'nieuw');
