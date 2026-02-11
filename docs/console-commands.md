# Console Commands Quick Reference

## Understand the App
| Command | What it shows |
|---------|--------------|
| `bin/console debug:router` | All routes (add `--show-controllers` for handler info) |
| `bin/console debug:container --tag=controller.service_arguments` | Controllers registered as services |
| `bin/console debug:autowiring` | Available autowirable services |
| `bin/console debug:config <bundle>` | Current config for a bundle |
| `bin/console debug:event-dispatcher` | All event listeners |
| `bin/console debug:twig` | Twig functions, filters, globals |
| `bin/console debug:firewall` | Security firewall configuration |

## Debug Issues
| Command | When to use |
|---------|------------|
| `bin/console debug:container <service>` | Check if a service exists and its class |
| `bin/console debug:router <route_name>` | Inspect a specific route |
| `bin/console router:match /some/path` | Find which route matches a URL |
| `bin/console lint:container` | Verify DI container compiles (catches type mismatches) |
| `bin/console lint:twig templates/` | Check Twig templates for syntax errors |
| `bin/console lint:yaml config/` | Validate YAML config files |

## Doctrine / Database
| Command | Purpose |
|---------|---------|
| `bin/console doctrine:schema:validate` | Check entity mappings match DB schema |
| `bin/console doctrine:mapping:info` | List all mapped entities |
| `bin/console doctrine:mapping:describe <Entity>` | Show fields/relations for an entity |
| `bin/console doctrine:fixtures:load` | Load test fixtures |

## DI Commands (Migration Complete)
| Command | Purpose |
|---------|---------|
| `bin/console debug:autowiring <type>` | Find autowirable service for a type hint |
| `bin/console debug:container --deprecations` | Show deprecated service usage |

## Cache
| Command | When to use |
|---------|------------|
| `bin/console cache:clear` | After config changes or when things are stale |
