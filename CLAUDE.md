# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Conventio** is a Symfony 8 web app for managing internship contracts between students, educational institutions (professors), and host organizations (tutors). It handles the full contract lifecycle: creation → tutor completion → professor approval → electronic signature via Yousign.

## Tech Stack

- **Backend:** Symfony 8.0, PHP 8.2+, Doctrine ORM, PostgreSQL 16
- **Frontend:** Twig, Bootstrap 5.3, Stimulus.js (Symfony UX), Turbo, AssetMapper (no Webpack)
- **External services:** Gotenberg (Docx→PDF), Yousign API v3 (e-signature), PHPOffice/PHPWord (.docx templates)
- **Email:** Mailpit in dev, configurable SMTP in production
- **Tests:** PHPUnit 12

## Common Commands

```bash
# Start Docker services (PostgreSQL, Mailpit)
docker compose up -d

# Start dev server
symfony server:start

# Database
php bin/console doctrine:migrations:migrate
php bin/console make:migration

# Cache
php bin/console c:c

# Assets
php bin/console importmap:install

# Tests
php bin/phpunit
php bin/phpunit tests/SomeTest.php

# Static analysis
vendor/bin/phpstan analyse
```

## Architecture

### Entity Model

`User` uses **single-table inheritance** (discriminator column) with three subclasses:
- `Student` — owns a collection of `Contract`s, belongs to a `Level`
- `Professor` — teaches `Level`s, can be a referent for a `Level`
- `Tutor` — linked to a `Contract`, represents the company supervisor

Central entities: `Contract` (status workflow), `Organisation` (host company), `InternshipDate` (academic period), `Level` (academic year), `Parameters` (system config).

### Contract Status Workflow

```
STARTED → FILLED_BY_TUTOR → APPROVED_PROF → APPROVED_DDFPT → SIGNED (or CANCELLED at any step)
```

### Controllers & Routes

Routes are attribute-based, auto-discovered from `src/Controller/`:

| Controller | Prefix | Role |
|---|---|---|
| `StudentContractController` | `/student/contract/` | `ROLE_STUDENT` |
| `ProfessorContractController` | `/professor/contracts/` | `ROLE_PROFESSOR` |
| `AdminController` | `/admin/` | `ROLE_ADMIN` |
| `ContractPdfController` | — | PDF download |
| `PublicContractController` | — | Token-based public sharing |

### Services (Business Logic)

- `GotenbergPdfService` — calls the Gotenberg HTTP API to convert rendered HTML to PDF
- `ContractDocumentService` — fills `.docx` Word templates with contract data via PHPOffice
- `YoutrustService` — creates Yousign signature requests and handles webhook callbacks

Business logic belongs in **Services**. Controllers must stay slim. Entities may contain state-related logic (rich domain model).

### Frontend

Stimulus controllers in `assets/controllers/` handle: work hours schedule UI, address auto-copy, dynamic form collections, client-side registration validation, CSRF token management.

## Coding Standards

- Always `declare(strict_types=1);` — all methods need parameter and return types
- Use PHP 8 **Attributes** for routing, ORM mapping, and DI (`#[Route]`, `#[ORM\Entity]`, `#[IsGranted]`)
- Constructor injection only — never `ContainerInterface`
- PSR-12 compliance; production-ready code (no placeholder comments)
- Tests follow Arrange-Act-Assert pattern

## Environment Variables

Key `.env` variables:

```bash
DATABASE_URL=postgresql://user:pass@host/conventio
GOTENBERG_URL=http://gotenberg:3000
YOUSIGN_API_URL=https://api-sandbox.yousign.app/v3
YOUSIGN_API_KEY=<secret>
MAILER_FROM_EMAIL=sio@lycee-faure.fr
APP_PUBLIC_URL=https://conventio-dev.lycee-faure.fr
```

## Deployment

Push to `main` → GitHub Actions (`.github/workflows/deploy-conventio-dev.yml`) auto-deploys to `/var/www/conventio` on a self-hosted runner: `composer install`, migrations, asset compilation.
