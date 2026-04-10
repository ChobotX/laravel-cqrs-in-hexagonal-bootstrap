#!/usr/bin/env bash
set -euo pipefail

VIEWS_DIR="${1:-resources/views}"
EXIT_CODE=0

# Checks multiline tags by reading the full file content before matching.
# This handles blade-formatter's force-aligned attribute wrapping where
# attributes span multiple lines.
# Before matching, strips Blade {{ }}, {!! !!}, and @directives(...) to
# avoid treating > inside PHP expressions as tag-closing >.
check_attr() {
    local tag="$1" attr="$2" label="$3" file="$4"

    local matches
    matches=$(perl -0777 -ne '
        my $orig = $_;
        # Replace Blade expressions with placeholders (no > inside)
        s/\{\{.*?\}\}/__BLADE__/sg;
        s/\{!!.*?!!\}/__BLADE__/sg;
        # Strip @directive(...) including nested parens (e.g. @class([... => ...]))
        s/\@\w+\((?:[^()]*|\((?:[^()]*|\([^()]*\))*\))*\)/__BLADE__/sg;
        while (/<('"$tag"')\b((?:(?!\/?>).)*?)>/sg) {
            my $attrs = $2;
            unless ($attrs =~ /\b'"$attr"'=/) {
                my $pos = pos() - length($&);
                # Count lines in the ORIGINAL content up to this position
                my $before = substr($orig, 0, $pos);
                my $line = ($before =~ tr/\n//) + 1;
                my $opening = $&;
                $opening =~ s/\n.*//s;
                print "$line:$opening\n";
            }
        }
    ' "$file" 2>/dev/null || true)

    if [ -n "$matches" ]; then
        echo "ERROR: <$tag> without $label attribute in $file"
        echo "$matches" | head -5
        EXIT_CODE=1
    fi
}

while IFS= read -r file; do
    check_attr "img" "alt" "alt" "$file"
    check_attr "svg" "aria-hidden" "aria-hidden" "$file"
    check_attr "th" "scope" "scope" "$file"
    check_attr "button" "(?:title|data-tooltip)" "title or data-tooltip" "$file"
    check_attr "a" "(?:title|data-tooltip)" "title or data-tooltip" "$file"
done < <(find "$VIEWS_DIR" -name '*.blade.php' -type f)

if [ "$EXIT_CODE" -eq 0 ]; then
    echo "OK: No accessibility issues found in Blade templates."
fi

exit $EXIT_CODE
