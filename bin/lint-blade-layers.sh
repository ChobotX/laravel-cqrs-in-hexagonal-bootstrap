#!/usr/bin/env bash
set -euo pipefail

VIEWS_DIR="${1:-resources/views}"
EXIT_CODE=0

while IFS= read -r file; do
    # Find references to App\ namespaces that are NOT App\Presentation
    # Matches: \App\Contract, \App\Domain, \App\Application, \App\Infrastructure
    # Also matches: app(App\... or app(\App\...
    # Excludes: App\Presentation (allowed in Blade templates)
    if grep -qnP '(?<!Presentation)(?:\\\\App\\\\(?:Contract|Domain|Application|Infrastructure)\\\\|app\s*\(\s*\\\\?App\\\\(?:Contract|Domain|Application|Infrastructure)\\\\)' "$file"; then
        lines=$(grep -nP '(?<!Presentation)(?:\\\\App\\\\(?:Contract|Domain|Application|Infrastructure)\\\\|app\s*\(\s*\\\\?App\\\\(?:Contract|Domain|Application|Infrastructure)\\\\)' "$file" | head -5)
        echo "ERROR: Non-Presentation App\\ reference found in $file"
        echo "$lines"
        EXIT_CODE=1
    fi
done < <(find "$VIEWS_DIR" -name '*.blade.php' -type f)

if [ "$EXIT_CODE" -eq 0 ]; then
    echo "OK: No non-Presentation layer references found in Blade templates."
fi

exit $EXIT_CODE
