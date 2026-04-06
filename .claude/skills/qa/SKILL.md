---
name: qa
description: >
  Smart QA runner. Auto-detects changed files and runs only relevant checks
  (linting, static analysis, tests, coverage). Use when verifying code changes,
  running lints, checking coverage, or before commits. Prevents redundant cycles.
argument-hint: "[full | backend | frontend | file1 file2 ...]"
allowed-tools: Bash Read Grep Glob
---

# QA — Smart Quality Checks

Run the minimum set of checks needed to verify your changes. Never waste time on irrelevant tools or redundant passes.

## Golden Rules

1. **Every failure must be fixed** — no exceptions, no excuses. Preexisting issues are your issues. Flaky tests are broken tests. Never dismiss, never re-run hoping it passes. Never suppress output or hide the problem. Take your time, read the full output, analyze the root cause deeply, and fix it properly.
2. **All commands through Sail** — `./vendor/bin/sail <command>`. Never run PHP, Composer, or npx on host.
3. **Never grep, pipe, or filter output** — read the full result directly. Output is already clean.
4. **Never run `check` after `check-and-fix`** — `check-and-fix` already verifies in its Wave 3. Running `check` after is 100% redundant.
5. **One QA pass per change cycle** — do not repeat checks that already passed. If pint passed, don't run pint again.
6. **Never run `check-and-fix` followed by `check`** — this is the single biggest time waste. Stop after `check-and-fix`.

## Mode Selection

| `$ARGUMENTS` | Mode |
|---|---|
| `full` | Full: `./vendor/bin/sail composer check-and-fix`. Done. |
| `backend` | Full backend: `./vendor/bin/sail composer check-and-fix -- --backend`. Done. |
| `frontend` | Full frontend: `./vendor/bin/sail composer check-and-fix -- --frontend`. Done. |
| File paths | Targeted: run checks on those specific files |
| *(empty)* | Smart targeted: auto-detect changes, run relevant subset |

For `full`, `backend`, `frontend` — run the single command, read output, stop. No follow-up checks.

## Smart Targeted Mode

When no arguments are provided, detect what changed and run only what's needed.

### Step 1: Detect Changed Files

```bash
# Combine all: unstaged, staged, and untracked
git diff --name-only HEAD
git diff --name-only --cached
git ls-files --others --exclude-standard
```

Deduplicate the combined list. If you already know which files you just edited, you can skip this step and use those files directly.

### Step 2: Classify and Check for Escalation

**Auto-escalate to full mode** if any of these changed:
- Config files: `phpstan.neon`, `rector.php`, `pint.json`, `composer.json`, `biome.json`, `vite.config.js`, `phpunit*.xml`, `tsconfig.json`, `tailwind.config.js`
- Shell scripts: `bin/*.sh`
- Architecture tests: `tests/Architecture/**`
- 15+ files across multiple categories

On escalation, switch to: `./vendor/bin/sail composer check-and-fix`. Done.

### Step 3: Run Targeted Checks

Classify each changed file and run only the relevant tools. All commands via `./vendor/bin/sail`.

#### PHP Production Code

For files in `app/**/*.php` or `database/**/*.php`:

```bash
# Fix (sequential — rector before pint to avoid conflicts)
./vendor/bin/sail php vendor/bin/rector <files>
./vendor/bin/sail php vendor/bin/pint <files>

# Verify (pint --test is redundant after pint fix — only phpstan needed)
./vendor/bin/sail php vendor/bin/phpstan analyse --memory-limit=512M <files>
```

PHPStan loads full project config even with specific files — this is correct and safe.

#### PHP Test Files

For files in `tests/**/*.php`:

```bash
# Fix style then verify
./vendor/bin/sail php vendor/bin/pint <files>
./vendor/bin/sail php vendor/bin/phpstan analyse --memory-limit=512M <files>

# Run those specific tests
./vendor/bin/sail php vendor/bin/pest <test-files>
```

No coverage check needed when only test files changed (source code unchanged).

#### Coverage — Only Affected Layers

Determine which layers need coverage based on changed source files:

| Changed path | Coverage config |
|---|---|
| `app/Domain/**` | `phpunit.domain-coverage.xml` |
| `app/Infrastructure/**` | `phpunit.infrastructure-coverage.xml` |
| `app/Presentation/**` (not views) | `phpunit.presentation-coverage.xml` |
| `app/Application/**` or `app/Contract/**` | All three above |
| Only `tests/**` changed | Skip coverage entirely |

Run only affected layers:

```bash
# Example: only domain changed
./vendor/bin/sail php vendor/bin/pest --configuration=phpunit.domain-coverage.xml --coverage --min=100
```

Do NOT run all three layer coverage checks when only one layer is affected. Each coverage run executes the full test suite — skipping unaffected layers saves minutes.

#### Blade Templates

For files in `resources/views/**/*.blade.php`:

```bash
# Fix formatting
./vendor/bin/sail npx blade-formatter --write <files>

# Lint (these are fast grep scripts — always run all, no need to target)
./vendor/bin/sail bash bin/lint-blade-no-js.sh
./vendor/bin/sail bash bin/lint-blade-a11y.sh
./vendor/bin/sail bash bin/lint-blade-url.sh
./vendor/bin/sail bash bin/lint-blade-layers.sh
```

#### JavaScript / TypeScript / Vue

For files in `resources/js/**/*.ts` or `resources/js/**/*.vue`:

```bash
# Fix
./vendor/bin/sail npx biome check --write <files>

# Verify (vitest is fast — always run full suite)
./vendor/bin/sail npx vitest run --coverage
./vendor/bin/sail bash bin/lint-catch-blocks.sh
```

#### CSS / Frontend Assets

For files in `resources/css/**` or any frontend change:

```bash
./vendor/bin/sail npx vite build
```

### Step 4: Read Output and Act

- Read the **complete** output of each command
- **Every failure must be fixed** — no exceptions. Preexisting failures are your failures too. If a check fails on code you didn't write, fix it anyway.
- **Never dismiss failures as flaky or transient** — flaky tests are broken tests. Investigate the root cause and fix it. "It passed when I ran it again" is not acceptable.
- On failure: diagnose from the output directly, fix the root cause, then re-run only the failed check to confirm the fix
- On success: done. Do not run additional verification passes.

## Common Mistakes to Avoid

- **Dismissing a failure as "flaky" or "transient"** — every failure has a root cause. Find it and fix it.
- **Excusing preexisting failures** — if it fails, it's broken. Fix it regardless of who introduced it.
- **Re-running a failed check without fixing anything** — hoping it passes next time is not a strategy.
- **Suppressing or truncating failure output** — always read the complete output and analyze the root cause before acting.
- Running `composer check` "just to be safe" after targeted checks passed — the targeted checks ARE verification
- Grepping coverage output for a percentage — read the full output, the pass/fail is in the summary
- Running coverage 3 times to check each layer when you only changed Domain files — run only Domain coverage
- Running the full frontend pipeline when you only changed PHP files
- Running pint twice (once to fix, once with `--test`) on files that pint just fixed — if pint fix succeeded, `--test` will pass
