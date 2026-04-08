#!/usr/bin/env bash
set -euo pipefail

JS_DIR="${1:-resources/js}"
EXIT_CODE=0

# ── Rule 1: Vue files must use PascalCase ────────────────────────
while IFS= read -r file; do
    basename=$(basename "$file")
    if [[ ! "$basename" =~ ^[A-Z] ]]; then
        echo "ERROR: Vue file must use PascalCase: $file"
        EXIT_CODE=1
    fi
done < <(find "$JS_DIR" -name '*.vue' -type f)

# ── Rule 2: TS files must use correct casing ─────────────────────
# - kebab-case for regular files (dialog-queue.ts, session-guard.ts)
# - camelCase allowed for composables (useDataGrid.ts) — Vue convention
# - PascalCase allowed for component test files (ChipSelector.test.ts)
while IFS= read -r file; do
    basename=$(basename "$file")
    # Skip composables (useXxx.ts, useXxx.test.ts) — Vue camelCase convention
    if [[ "$basename" =~ ^use[A-Z] ]]; then
        continue
    fi
    # Skip PascalCase test files — they mirror their Vue component name
    if [[ "$basename" =~ ^[A-Z].*\.test\.ts$ ]]; then
        continue
    fi
    if [[ ! "$basename" =~ ^[a-z][a-z0-9.-]*\.ts$ ]]; then
        echo "ERROR: TS file must use kebab-case (composables: camelCase, component tests: PascalCase): $file"
        EXIT_CODE=1
    fi
done < <(find "$JS_DIR" -name '*.ts' -type f ! -name '*.d.ts')

# ── Rule 3: No index.ts barrel files ────────────────────────────
while IFS= read -r file; do
    echo "ERROR: Barrel file (index.ts) not allowed: $file"
    EXIT_CODE=1
done < <(find "$JS_DIR" -name 'index.ts' -type f 2>/dev/null)

# ── Rule 4: Only *-app.ts files may call createApp ──────────────
while IFS= read -r file; do
    basename=$(basename "$file")
    if [[ ! "$basename" =~ -app\.ts$ ]]; then
        lines=$(grep -n 'createApp' "$file" | head -3)
        echo "ERROR: createApp() only allowed in *-app.ts files: $file"
        echo "$lines"
        EXIT_CODE=1
    fi
done < <(grep -rl 'createApp' "$JS_DIR" --include='*.ts' 2>/dev/null || true)

# ── Rule 5: Widget dirs with Vue files must have *-app.ts ────────
# Widget directories that contain .vue files must have at least one
# *-app.ts bootstrapper somewhere within.
# Excluded: shared/ (library components used inside other widgets).
while IFS= read -r dir; do
    if ! find "$dir" -name '*-app.ts' -type f 2>/dev/null | grep -q .; then
        echo "ERROR: Widget directory with Vue files must have a *-app.ts bootstrapper: $dir"
        EXIT_CODE=1
    fi
done < <(find "$JS_DIR/widgets" -maxdepth 1 -mindepth 1 -type d 2>/dev/null | while read -r dir; do
    if find "$dir" -name '*.vue' -type f 2>/dev/null | grep -q .; then
        echo "$dir"
    fi
done)

# ── Rule 6: No loose files at resources/js/ root ────────────────
# Only app.ts is allowed at the root level. Everything else must be
# categorized into core/, behaviors/, shared/, widgets/, or types/.
while IFS= read -r file; do
    basename=$(basename "$file")
    if [[ "$basename" != "app.ts" ]]; then
        echo "ERROR: No loose files at resources/js/ root (must be in core/, behaviors/, shared/, or widgets/): $file"
        EXIT_CODE=1
    fi
done < <(find "$JS_DIR" -maxdepth 1 -type f -name '*.ts' -o -name '*.vue' 2>/dev/null)

if [ "$EXIT_CODE" -eq 0 ]; then
    echo "OK: Frontend structure checks passed."
fi

exit $EXIT_CODE
