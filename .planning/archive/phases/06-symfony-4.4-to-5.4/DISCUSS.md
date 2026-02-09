# Sprint 6: Symfony 4.4 → 5.4

## Intent

Upgrade to Symfony 5.4 LTS. Remove APIs deleted in 5.0 (AdvancedUserInterface, Role base class, old event dispatch). Keep deprecated-but-functional APIs (SwiftMailer, password_encoder) for Sprint 7.

## Deliverables

- [x] Composer packages at 5.4.*
- [x] User entity: UserInterface + PasswordAuthenticatedUserInterface
- [x] Role entity: standalone (no Symfony base class)
- [x] Security config: password_hashers, lazy anonymous, user_checker
- [x] Event dispatch signature flipped (27 calls)
- [x] BaseController → AbstractController + getSubscribedServices()
- [x] PHPUnit 9 compatibility (setUp/tearDown void, KernelBrowser)
- [ ] Controllers registered as services (test suite passing)

## Questions

- [x] Keep password_encoder service? → Yes, still works in 5.4
- [x] SwiftMailer now or later? → Later (Sprint 7), still works
- [x] RoutingExtension approach? → Composition (final class in Sf5)
