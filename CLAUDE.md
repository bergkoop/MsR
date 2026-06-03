# CLAUDE.md — Wealth Creators
> Marktplaats voor de $up€r Rich · Projectnummer 2023#004

This file tells Claude everything it needs to know about this project.
Read this before writing any code or giving any advice.

---

## Project summary

A luxury marketplace website built for the fictional client "Wealth Creators".
Visitors can browse exclusive products (supercars, yachts, helicopters, watches, jewellery),
view individual showcase pages, and submit bids via a contact form.
Users must register and log in to place bids. Bids are stored in a database and visible
on a personal bids page for the logged-in user.

---

## Tech stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Frontend   | HTML5, CSS3, Vanilla JavaScript   |
| Backend    | PHP 8 (plain, no framework)       |
| Database   | MySQL                             |
| Hosting    | School FTP server (no CLI access) |
| Editor     | Visual Studio Code                |
| Versioning | GitHub                            |

---

## Coding conventions

- **Language**: variable names and code in English, comments in Dutch
- **PHP**: use `mysqli` (procedural), not PDO
- **No frameworks**: no Laravel, no Composer, no npm builds
- **Includes**: always use `includes/header.php` and `includes/footer.php`
- **Indentation**: 4 spaces, no tabs
- **Quotes**: single quotes in PHP, double quotes in HTML attributes
- **SQL**: always use prepared statements with `mysqli_prepare()` — never string-concatenate user input into queries
- **Validation**: always validate both client-side (JS) and server-side (PHP)
- Keep files small and focused — one responsibility per file

---

## Folder structure

```
/
├── index.php                  # Homepage
├── overzicht.php              # Product overview with category filter
├── showcase.php               # Single product page (?id=X)
├── berichten.php              # Bids overview (login required)
├── verwerk_bod.php            # Handles bid form submission (no UI, login required)
├── registreren.php            # User registration form
├── inloggen.php               # Login form
├── uitloggen.php              # Destroys session, redirects to homepage
├── config.php                 # DB credentials (never commit this)
├── includes/
│   ├── header.php             # Shared nav + <head>
│   ├── footer.php             # Shared footer
│   ├── db.php                 # mysqli connection (included via config.php)
│   └── auth.php               # Session helpers: is_ingelogd(), huidige_gebruiker(), vereis_login()
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # Client-side validation + filter logic
│   └── img/                   # Product and category images
```

---

## Database

Database name: `wealth_creators`

### Tables

**categorieen**
```sql
id              INT         PK AUTO_INCREMENT
naam            VARCHAR(100) NOT NULL
beschrijving    TEXT
afbeelding_url  VARCHAR(500)
```

**producten**
```sql
id              INT          PK AUTO_INCREMENT
categorie_id    INT          FK → categorieen.id
naam            VARCHAR(200) NOT NULL
beschrijving    TEXT         NOT NULL
prijs           DECIMAL(15,2) NOT NULL
minimaal_bod    DECIMAL(15,2) NOT NULL
locatie         VARCHAR(150)
status          ENUM('beschikbaar','gereserveerd','verkocht') DEFAULT 'beschikbaar'
eigenaar_naam   VARCHAR(100) NOT NULL
aangemaakt_op   DATETIME     DEFAULT NOW()
```

**product_afbeeldingen**
```sql
id                INT          PK AUTO_INCREMENT
product_id        INT          FK → producten.id
afbeelding_url    VARCHAR(500) NOT NULL
is_hoofdafbeelding TINYINT(1)  DEFAULT 0
volgorde          INT          DEFAULT 0
```

**gebruikers**
```sql
id              INT          PK AUTO_INCREMENT
naam            VARCHAR(100) NOT NULL
email           VARCHAR(150) NOT NULL UNIQUE
wachtwoord_hash VARCHAR(255) NOT NULL   -- password_hash(), never plain text
aangemaakt_op   DATETIME     DEFAULT NOW()
```

**biedingen**
```sql
id              INT          PK AUTO_INCREMENT
product_id      INT          FK → producten.id
gebruiker_id    INT          FK → gebruikers.id (nullable — old/seed bids have NULL)
naam            VARCHAR(100) NOT NULL
email           VARCHAR(150) NOT NULL
bod_bedrag      DECIMAL(15,2) NOT NULL
bericht         TEXT         NOT NULL
aangemaakt_op   DATETIME     DEFAULT NOW()
status          ENUM('nieuw','gelezen','beantwoord') DEFAULT 'nieuw'
```

### Key rules
- `bod_bedrag` must always be >= `minimaal_bod` of the related product.
  Enforce this in BOTH JavaScript (before submit) and PHP (before INSERT).
- Bidding requires login. `verwerk_bod.php` calls `vereis_login()` at the top.
  `gebruiker_id`, `naam`, and `email` are taken from `huidige_gebruiker()`, not POST.

---

## Pages

| File              | Route                  | Description                                                  |
|-------------------|------------------------|--------------------------------------------------------------|
| index.php         | /                      | Homepage: hero, categories, featured products                |
| overzicht.php     | /overzicht.php         | All products, filterable by category                         |
| showcase.php      | /showcase.php?id=X     | Single product: gallery, specs, bid form (login required)    |
| berichten.php     | /berichten.php         | Personal bid history for logged-in user (login required)     |
| verwerk_bod.php   | POST only              | Validates + saves bid, then redirects (login required)       |
| registreren.php   | /registreren.php       | Registration form: name, email, password                     |
| inloggen.php      | /inloggen.php          | Login form; supports ?retour= for post-login redirect        |
| uitloggen.php     | /uitloggen.php         | Destroys session, redirects to homepage                      |

---

## Design

- Style: cool, modern, tech-luxury — dark navy background, electric blue accent, silver/platinum highlights
- Color palette: `#0A0F1E` bg · `#1E6FFF` accent · `#C0C8D8` highlight · `#FFFFFF` text
- Fonts: Cormorant Garamond (headings) + Inter (body) — both via Google Fonts
- Inspired by: Omega Watches + Sunreef Yachts — cinematic, lots of whitespace, editorial
- Must be responsive (mobile = single column)

---

## Validation rules

- Bid amount: required, numeric, >= product `minimaal_bod`
- Name: required, min 2 characters, max 100
- Email: required, valid email format
- Message: required, min 10 characters
- Never trust client-side validation alone — always re-validate in PHP

---

## Security rules

- Use `mysqli_real_escape_string()` or prepared statements — never raw user input in SQL
- Use `htmlspecialchars()` when outputting any user data to HTML
- Check that `bod_bedrag >= minimaal_bod` server-side before every INSERT
- `config.php` must never be committed to GitHub — add it to `.gitignore`
---
## maybe's
- Email notifications
---

## What is NOT in scope

- Payment processing
- Admin dashboard / CMS
- Live bidding / real-time updates

---

## Current status

- [x] Projectplan written
- [x] Moodboard made
- [x] Wireframes made
- [x] Database created
- [x] PHP structure set up
- [x] Homepage built
- [x] Overview page built
- [x] Showcase page built
- [x] Bids page built
- [x] User accounts (register / login / logout)
- [ ] Tested + uploaded to FTP
