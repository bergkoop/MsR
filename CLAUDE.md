# CLAUDE.md — Wealth Creators
> Marktplaats voor de $up€r Rich · Projectnummer 2023#004

This file tells Claude everything it needs to know about this project.
Read this before writing any code or giving any advice.

---

## Project summary

A luxury marketplace website built for the fictional client "Wealth Creators".
Visitors can browse exclusive products (supercars, yachts, helicopters, watches, jewellery),
view individual showcase pages, and submit bids via a contact form.
Bids are stored in a database and visible on a personal bids page per email address.

---

## Tech stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Frontend   | HTML5, CSS3, Vanilla JavaScript   |
| Backend    | PHP 8 (plain, no framework)       |
| Database   | MariaDB / MySQL                   |
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
├── berichten.php              # Bids overview (lookup by email)
├── verwerk_bod.php            # Handles bid form submission (no UI)
├── config.php                 # DB credentials (never commit this)
├── includes/
│   ├── header.php             # Shared nav + <head>
│   ├── footer.php             # Shared footer
│   └── db.php                 # mysqli connection (included via config.php)
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

**biedingen**
```sql
id              INT          PK AUTO_INCREMENT
product_id      INT          FK → producten.id
naam            VARCHAR(100) NOT NULL
email           VARCHAR(150) NOT NULL
bod_bedrag      DECIMAL(15,2) NOT NULL
bericht         TEXT         NOT NULL
aangemaakt_op   DATETIME     DEFAULT NOW()
status          ENUM('nieuw','gelezen','beantwoord') DEFAULT 'nieuw'
```

### Key rule
`bod_bedrag` must always be >= `minimaal_bod` of the related product.
Enforce this in BOTH JavaScript (before submit) and PHP (before INSERT).

---

## Pages

| File              | Route                  | Description                                      |
|-------------------|------------------------|--------------------------------------------------|
| index.php         | /                      | Homepage: hero, categories, featured products    |
| overzicht.php     | /overzicht.php         | All products, filterable by category             |
| showcase.php      | /showcase.php?id=X     | Single product: gallery, specs, bid form         |
| berichten.php     | /berichten.php         | Bid history looked up by email (no login needed) |
| verwerk_bod.php   | POST only              | Validates + saves bid, then redirects            |

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
- User login / accounts / passwords
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
- [ ] Database created
- [ ] PHP structure set up
- [ ] Homepage built
- [ ] Overview page built
- [ ] Showcase page built
- [ ] Bids page built
- [ ] Tested + uploaded to FTP
