# PHPStan architecture rule fixtures

These PHP files contain **intentional** violations (or clean control cases) for custom PHPStan rules. They are excluded from the main PHPStan run via `phpstan.neon` `excludePaths` and are only analysed through `PHPStan\Testing\RuleTestCase` subclasses in this directory.

## Conventions

- This directory is listed in `pint.json` `exclude` so a **full-project** `pint` run does not rewrite intentional `use { … }` group imports (Pint’s `single_import_per_statement` rule would otherwise flatten them). Do not run `pint` with an explicit path to a single file here — Pint ignores `exclude` in that mode and will still rewrite the file.
- Prefer small, focused files — one primary scenario per fixture.
- Namespace fixtures under `App\Infrastructure\Simulator*` or `App\Domain\...\Simulator*` so they do not collide with production code.
- **Register every new `.php` file** in at least one `*RuleTest.php` by passing its path to `$this->analyse([__DIR__.'/Fixtures/…'], …)`. `PhpStanFixtureFilesReferencedInRuleTestsTest` fails if a fixture is orphaned.
- Shared symbols for `use function` / `use const` imports live under `UserPhpStanFixtureInternal/` (composer autoload-dev) so rules can resolve referenced names.
