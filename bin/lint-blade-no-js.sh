#!/usr/bin/env bash
set -euo pipefail

VIEWS_DIR="${1:-resources/views}"
EXIT_CODE=0

while IFS= read -r file; do
    # Check for <script> tags
    if grep -qnP '<script[\s>]' "$file"; then
        lines=$(grep -nP '<script[\s>]' "$file" | head -5)
        echo "ERROR: Inline <script> tag found in $file"
        echo "$lines"
        EXIT_CODE=1
    fi

    # Check for inline event handlers (onclick, onchange, onsubmit, etc.)
    if grep -qnP '\bon[a-z]+\s*=' "$file"; then
        lines=$(grep -nP '\bon[a-z]+\s*=' "$file" | head -5)
        echo "ERROR: Inline event handler found in $file"
        echo "$lines"
        EXIT_CODE=1
    fi

    # Check for javascript: URLs
    if grep -qnPi 'href\s*=\s*["\x27]javascript:' "$file"; then
        lines=$(grep -nPi 'href\s*=\s*["\x27]javascript:' "$file" | head -5)
        echo "ERROR: javascript: URL found in $file"
        echo "$lines"
        EXIT_CODE=1
    fi
done < <(find "$VIEWS_DIR" -name '*.blade.php' -type f)

if [ "$EXIT_CODE" -eq 0 ]; then
    echo "OK: No inline JavaScript found in Blade templates."
fi

exit $EXIT_CODE
