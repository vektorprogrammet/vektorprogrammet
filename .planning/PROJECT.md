# Project: Vektorprogrammet Symfony Upgrade

## Problem

Monolith is stuck on Symfony 3.4 (EOL 2021). Cannot use modern PHP, security patches, or ecosystem packages.

## Solution

Incremental upgrade through 4.4 → 5.4 → 6.4, one major version at a time. Each sprint is a self-contained, committable unit that leaves tests passing.

## Success

- [ ] Running on Symfony 6.4 LTS with PHP 8.2+
- [ ] All 496 tests pass (minus 3 pre-existing failures)
- [ ] No deprecated APIs in use
- [ ] Modern project structure (attributes, DI, Mailer)

## Constraints

Stack: Symfony, Doctrine ORM, Twig, PHPUnit, MySQL, PHP
- Must maintain backwards compatibility with existing routes/URLs
- No data migrations — schema unchanged
- Each sprint must leave tests in passing state (≤3 failures)
- SwiftMailer still works in 5.x, migrate in 7

## Out of Scope

- Frontend rewrite (Sprint 8-9)
- Database schema changes
- New features
- symfony/flex adoption
