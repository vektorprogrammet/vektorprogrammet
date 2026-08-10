# Blocked unique constraints (guard-parity A2 and A7)

Guard-parity items A1, A2, A6 and A7 added four uniqueness invariants as ORM metadata with
no migration behind them. Production is migration-driven, so none of the four existed in
production. `migrations/Version20260810002046.php` ships the two that production can accept
today. This file tracks the two it cannot.

## Status

| Item | Table | Key (as enforced by the entity) | Production scan | State |
|---|---|---|---|---|
| A1 | `school_capacity` | `school_id, semester_id, department_id` | 0 duplicate groups | **shipped** in `Version20260810002046` |
| A6 | `receipt` | `visual_id` | 0 duplicates, 0 nulls | **shipped** in `Version20260810002046` |
| A2 | `assistant_history` | `user_id, school_id, semester_id, bolk` | ~3 same-bolk collisions *after* normalization | **blocked** on a data-shape migration |
| A7 | `team_membership` | `user_id, team_id, start_semester_id, position_id` | 7 duplicate groups | **blocked** on data cleanup |

Scan source: production backup `vektor-backup_2024-08-22.sql` (2923 users). A backup is a
point-in-time reading, so the scans below must be re-run against the live database
immediately before any migration. `Version20260810002046::preUp()` re-runs its own two scans
and aborts before touching the schema if they are not clean.

## Three of the four keys were mis-modeled, not just unmigrated

The audit specified keys that production refutes. In each case the audit's key would have
made valid data unrepresentable:

```
school_capacity   (school, semester)             -> 3 collisions
                  (school, semester, department) -> 0     one school, one semester, two
                                                          departments is valid data

team_membership   (user, team, start_semester)        -> 13 collisions
                  of which differ only by position_id ->  6  one person holds two
                                                            positions in one team
                  true duplicates remaining           ->  7

assistant_history (user, school, semester)        -> 9 collisions
                  ... but a semester has TWO teaching blocks (bolk) and an assistant is
                  sent out once per assigned bolk, on a possibly different weekday. Doing
                  both bolks at one school is two legitimate placements, so bolk belongs
                  in the key.
```

The entities were corrected to the real keys. `SchoolCapacity`, `TeamMembership` and
`AssistantHistory` carry the reasoning inline next to the `#[ORM\UniqueConstraint]`.

## A2 is a data-shape migration, not a constraint-add

`assistant_history.bolk` is a free-form string. Production holds only four values:

| `bolk` | rows | meaning |
|---|---|---|
| `Bolk 1` | 942 | one placement in block 1 |
| `Bolk 2` | 521 | one placement in block 2 |
| `Bolk 1, Bolk 2` | 332 | **two** placements stored in one row |
| `NULL` | 20 | unrecorded |

The 332 comma rows are the same fact written twice into one cell: one row is claiming to be
two placements, which is why the entity needs `AssistantHistory::isGroup()` to go
`str_contains($bolk, "Bolk $group")` to read it back. Adding a unique index on top of that
shape would freeze the violation rather than fix it, so A2 needs, in order:

1. **Close the value sets.** `Bolk` becomes an enum (`Bolk 1 | Bolk 2`) and the weekday
   becomes an enum (`Mandag`..`Fredag`), replacing the free-form strings and the
   `/^Bolk \d+(, Bolk \d+)*$/` regex that currently permits the comma shape.
2. **Split the 332 comma rows**, one row per bolk, copying every other column:

   ```sql
   -- preview first; run inside a transaction with a backup taken
   INSERT INTO assistant_history (user_id, semester_id, department_id, school_id, workdays, bolk, day)
   SELECT user_id, semester_id, department_id, school_id, workdays, 'Bolk 2', day
   FROM assistant_history
   WHERE bolk = 'Bolk 1, Bolk 2';

   UPDATE assistant_history SET bolk = 'Bolk 1' WHERE bolk = 'Bolk 1, Bolk 2';
   ```

   The weekday is copied because the source row records only one; if the two bolks ran on
   different weekdays that information was already lost in the comma shape and has to be
   re-entered by hand afterwards.
3. **Reconcile the residual collisions.** After the split roughly 3 genuine same-bolk
   duplicates remain (workday corrections saved as a second row). Inspect and delete those
   by hand -- see the scan below.
4. **Then** add `unique_user_school_semester_bolk`.

Only step 3 is a delete. Do not "clean up" A2 by deleting the 9 rows the audit's key
reported: 6 of those groups are the comma rows and a real second placement, and deleting
them would erase a placement that happened.

## Pre-apply gate: the duplicate scans

Run each against the live database. A migration may only be applied when its scan returns
zero rows.

```sql
-- A1  school_capacity (shipped; re-checked automatically by preUp)
SELECT school_id, semester_id, department_id, COUNT(*) AS n
FROM school_capacity
WHERE school_id IS NOT NULL AND semester_id IS NOT NULL AND department_id IS NOT NULL
GROUP BY school_id, semester_id, department_id
HAVING COUNT(*) > 1;

-- A6  receipt (shipped; re-checked automatically by preUp)
SELECT visual_id, COUNT(*) AS n
FROM receipt
WHERE visual_id IS NOT NULL
GROUP BY visual_id
HAVING COUNT(*) > 1;

-- A2  assistant_history (BLOCKED: run AFTER the comma rows are split; ~3 rows expected)
SELECT user_id, school_id, semester_id, bolk, COUNT(*) AS n
FROM assistant_history
WHERE user_id IS NOT NULL AND school_id IS NOT NULL
  AND semester_id IS NOT NULL AND bolk IS NOT NULL
GROUP BY user_id, school_id, semester_id, bolk
HAVING COUNT(*) > 1;

-- A2  the comma rows that must be split before the scan above means anything
SELECT COUNT(*) FROM assistant_history WHERE bolk LIKE '%,%';

-- A7  team_membership (BLOCKED: 7 groups)
SELECT user_id, team_id, start_semester_id, position_id, COUNT(*) AS n
FROM team_membership
WHERE user_id IS NOT NULL AND team_id IS NOT NULL
  AND start_semester_id IS NOT NULL AND position_id IS NOT NULL
GROUP BY user_id, team_id, start_semester_id, position_id
HAVING COUNT(*) > 1;
```

`NULL` parts are excluded because MySQL treats `NULL` as distinct inside a unique index, so
a row with a `NULL` key part can never collide. Including them would over-report.

## Cleanup is an operator decision, not an automated step

Do **not** delete duplicates from a migration. Each duplicate group is a small piece of
history that someone has to look at. To inspect a group before deciding:

```sql
-- the assistant_history rows behind the residual A2 collisions (post-split)
SELECT h.*
FROM assistant_history h
JOIN (
    SELECT user_id, school_id, semester_id, bolk
    FROM assistant_history
    WHERE user_id IS NOT NULL AND school_id IS NOT NULL
      AND semester_id IS NOT NULL AND bolk IS NOT NULL
    GROUP BY user_id, school_id, semester_id, bolk
    HAVING COUNT(*) > 1
) d ON d.user_id = h.user_id AND d.school_id = h.school_id
   AND d.semester_id = h.semester_id AND d.bolk = h.bolk
ORDER BY h.user_id, h.school_id, h.semester_id, h.bolk, h.id;

-- the team_membership rows behind the A7 collisions
SELECT m.*
FROM team_membership m
JOIN (
    SELECT user_id, team_id, start_semester_id, position_id
    FROM team_membership
    WHERE user_id IS NOT NULL AND team_id IS NOT NULL
      AND start_semester_id IS NOT NULL AND position_id IS NOT NULL
    GROUP BY user_id, team_id, start_semester_id, position_id
    HAVING COUNT(*) > 1
) d ON d.user_id = m.user_id AND d.team_id = m.team_id
   AND d.start_semester_id = m.start_semester_id AND d.position_id = m.position_id
ORDER BY m.user_id, m.team_id, m.start_semester_id, m.position_id, m.id;
```

## The follow-up migration

A7 only needs its 7 duplicate groups reconciled. A2 needs the whole data-shape migration
above first. Once both scans return zero rows, add a migration whose `up()` is:

```php
$this->addSql('CREATE UNIQUE INDEX unique_user_school_semester_bolk ON assistant_history (user_id, school_id, semester_id, bolk)');
$this->addSql('CREATE UNIQUE INDEX unique_user_team_semester_position ON team_membership (user_id, team_id, start_semester_id, position_id)');
```

and whose `down()` drops the same two indexes. Copy the `preUp()` gate from
`Version20260810002046` so the follow-up refuses to run against dirty data as well. The
index names above are the ones Doctrine derives from the current entity metadata; keeping
them identical is what keeps `doctrine:migrations:diff` empty.

Until that lands, `assistant_history` and `team_membership` are enforced in the ORM metadata
and in the test schema, but **not** in production. That gap is deliberate and visible here
rather than silent.
