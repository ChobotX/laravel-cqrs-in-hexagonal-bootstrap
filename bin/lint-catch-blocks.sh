#!/usr/bin/env bash
set -euo pipefail

JS_DIR="${1:-resources/js}"
EXIT_CODE=0

# Checks that every catch block in .ts and .vue files either:
# - rethrows (throw)
# - logs via logger module (logError, logWarning, logInfo, logDebug, or logger imports)
# - shows user feedback (showErrorToast, errorMessage)
# - has a @silent comment explaining intentional suppression
#
# Uses Perl for multiline matching with brace-depth counting, same approach
# as lint-blade-a11y.sh.

while IFS= read -r file; do
    matches=$(perl -0777 -ne '
        my $content = $_;
        my @violations;

        # Pattern 1: .catch( ... => { ... })  (Promise-style)
        # Pattern 2: } catch (...) { ... }     (try/catch-style)
        while ($content =~ /
            (?:
                \.catch\s*\(\s*\(?[^)]*\)?\s*=>\s*\{  # .catch(() => {
                |
                \}\s*catch\s*(?:\([^)]*\))?\s*\{       # } catch (...) {
            )
        /gx) {
            my $match_start = $-[0];
            my $brace_start = pos($content) - 1;

            # Count lines to match start
            my $before = substr($content, 0, $match_start);
            my $line = ($before =~ tr/\n//) + 1;

            # Extract block body using brace-depth counting
            my $depth = 1;
            my $i = $brace_start + 1;
            while ($i < length($content) && $depth > 0) {
                my $ch = substr($content, $i, 1);
                $depth++ if $ch eq "{";
                $depth-- if $ch eq "}";
                $i++;
            }
            my $body = substr($content, $brace_start + 1, $i - $brace_start - 2);

            # Check for allowed patterns
            my $ok = 0;
            $ok = 1 if $body =~ /\bthrow\b/;
            $ok = 1 if $body =~ /\blog(?:Error|Warning|Info|Debug)\b/;
            $ok = 1 if $body =~ /\b(?:error|warn|info|debug)\s*\(/  # direct logger imports
                        && $body !~ /showErrorToast/;  # only if not already a toast alias
            $ok = 1 if $body =~ /\bshowErrorToast\b/;
            $ok = 1 if $body =~ /\berrorMessage\b/;
            $ok = 1 if $body =~ /\@silent/;

            push @violations, $line unless $ok;
        }

        for my $v (@violations) {
            print "  line $v\n";
        }
    ' "$file" 2>/dev/null || true)

    if [ -n "$matches" ]; then
        echo "ERROR: Silent catch block(s) in $file"
        echo "$matches"
        EXIT_CODE=1
    fi
done < <(find "$JS_DIR" -type f \( -name '*.ts' -o -name '*.vue' \) ! -name '*.test.ts' ! -name '*.d.ts')

if [ "$EXIT_CODE" -eq 0 ]; then
    echo "OK: No silent catch blocks found in TypeScript/Vue files."
fi

exit $EXIT_CODE
