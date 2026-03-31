# App — Cross-Layer Rules

## Class rules

PHPat structural enforcement — violations fail `composer analyse`.

| Layer | Rule |
|---|---|
| Contract | interfaces only |
| Domain | `final readonly` (interfaces and throwables skip `readonly`) |
| Infrastructure | `final` |
| Presentation | `final` (traits excluded) |

Additional rules:
- **Controllers** (`App\Presentation\Http\Controller`): single `__invoke()` method (invokable)
- **No App→App inheritance**: no class in `App\` may extend another `App\` class
- **No generic PHP exceptions**: never construct `Exception`, `RuntimeException`, `LogicException`, `InvalidArgumentException`, `BadMethodCallException`, `DomainException` (PHP's), `RangeException`, `OverflowException`, `UnderflowException`, `UnexpectedValueException`, `LengthException`, `OutOfRangeException`, `OutOfBoundsException` — create domain-specific exceptions instead

## Code style

Enforced by Pint (`pint.json`) and Rector (`rector.php`). Key rules:

- `declare(strict_types=1)` — every PHP file
- `===` only — no loose `==` comparisons
- Import everything — classes, functions, constants; order: `class`, `function`, `const`
- No `@var` overrides — use type narrowing with `is_string()`, `instanceof`, or proper exceptions
- No `mixed` — except in generic interface signatures
- Class member order: traits → constants → properties → constructor → methods
- `protected` → `private` — use `private` unless interface requires otherwise
- No empty phpdoc, no superfluous phpdoc tags
- `DateTimeImmutable` over `DateTime`
- Single quotes for strings
- Trailing commas in multiline
- Blank line before `return` and `throw`
- Void return types explicit
- No magic strings — string literals in `===`/`!==` and `match()` arms must use enums or class constants (empty string `''` excluded)
- No magic numbers — numeric literals (except `0`, `1`, `-1`) must use class constants; includes const definitions and enum case values as exceptions

## Blade style

Enforced by blade-formatter (`.bladeformatterrc.json`). Key rules:

- 4-space indentation, 120-char line width, LF line endings
- Force-aligned attribute wrapping (triggers at 2+ attributes)
- Code-guide HTML attribute ordering (class → id → data-* → for/type/href → aria-*)
- Tailwind CSS class sorting enabled
- No multiple empty lines
- PHP syntax checking enabled
