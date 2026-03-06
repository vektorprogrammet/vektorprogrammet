---
paths:
  - "templates/**/*.twig"
---

# Twig 3

No `for...if` — use `|filter()` instead. Without this: Twig 3 deprecation then error.

No blocks inside `if` — put conditional inside the block. Without this: Twig 3 compilation error.

`{% apply spaceless %}...{% endapply %}` not `{% spaceless %}`. Without this: Twig 3 deprecated tag error.
