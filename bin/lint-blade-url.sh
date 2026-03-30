#!/usr/bin/env bash
set -euo pipefail

VIEWS_DIR="${1:-resources/views}"
EXIT_CODE=0

# Banned: request()->url(), request()->fullUrl(), request()->getUri()
# These return the raw HTTP scheme from the underlying request, which differs
# from the configured APP_URL when a reverse proxy terminates SSL.
# Result: links use http:// while SESSION_SECURE_COOKIE expects https://,
# so the browser drops the session cookie and the user gets a 302 to login.
#
# Safe alternatives:
#   url()->current()   — current URL with correct scheme
#   url()->full()      — current URL with query string and correct scheme
#   route(...)         — named route with correct scheme
PATTERN='request()->url()\|request()->fullUrl()\|request()->getUri('

while IFS= read -r file; do
    if grep -qn "$PATTERN" "$file"; then
        lines=$(grep -n "$PATTERN" "$file" | head -5)
        echo "ERROR: Unsafe request()->url()/fullUrl()/getUri() in $file"
        echo "       Use url()->current(), url()->full(), or route() instead."
        echo "$lines"
        EXIT_CODE=1
    fi
done < <(find "$VIEWS_DIR" -name '*.blade.php' -type f)

if [ "$EXIT_CODE" -eq 0 ]; then
    echo "OK: No unsafe URL generation found in Blade templates."
fi

exit $EXIT_CODE
