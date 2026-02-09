# Sprint 7: Symfony 5.4 → 6.4

## Intent

Final major upgrade to Symfony 6.4 LTS. Remove all Sf5-deprecated APIs. Modernize to PHP 8 idioms (attributes, typed properties). Switch to Symfony Mailer.

## Deliverables

- [ ] Composer packages at 6.4.*
- [ ] SwiftMailer → Symfony Mailer (MAILER_DSN)
- [ ] Annotations → PHP 8 attributes (routes, Doctrine mappings)
- [ ] getDoctrine() → constructor-injected repositories
- [ ] $this->get() → constructor DI (100+ calls)
- [ ] password_encoder → password_hasher service
- [ ] Remove all remaining deprecation warnings

## Questions

- [ ] Use rector for automated annotation→attribute conversion?
- [ ] Batch getDoctrine() removal or file-by-file?
- [ ] Keep FOS REST Bundle or switch to native Symfony controllers?

## Risk Areas

- SwiftMailer migration touches mailer services, templates, and test mocks
- 100+ $this->get() calls — biggest single task, risk of regressions
- Annotation→attribute is mechanical but high file count

## Reference: vektor-backend

Existing modernization at `github.com/vektorprogrammet/vektor-backend` reached Sf5.4/PHP 8.2. Key takeaways:
- `Controller` → `AbstractController` with constructor-injected `ManagerRegistry`
- `AdvancedUserInterface` → `UserInterface` + `PasswordAuthenticatedUserInterface`
- SwiftMailer → `symfony/mailer` with `MAILER_DSN`
- `encoders` → `password_hashers`, `enable_authenticator_manager: true`
- Used `rector/rector` for automated refactoring
- Reference files: `composer.json`, `config/packages/security.yaml`, `src/Entity/User.php`
- Consider `dama/doctrine-test-bundle` for faster DB test transactions
