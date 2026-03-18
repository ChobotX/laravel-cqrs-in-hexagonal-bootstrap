# Shared helpers for check scripts — sourced, not executed.
# Caller must set -euo pipefail before sourcing.

GREEN='\033[0;32m' RED='\033[0;31m' BLUE='\033[0;34m' NC='\033[0m'
header() { echo -e "\n${BLUE}=== $1 ===${NC}\n"; }
pass()   { echo -e "${GREEN}✓ $1${NC}"; }
fail()   { echo -e "${RED}✗ $1${NC}"; }

FAILED=0
RUN_FRONTEND=1
RUN_BACKEND=1
ROOT=""

while [[ $# -gt 0 ]]; do
    case "$1" in
        --frontend) RUN_FRONTEND=1; RUN_BACKEND=0; shift ;;
        --backend)  RUN_BACKEND=1; RUN_FRONTEND=0; shift ;;
        --root)     ROOT="$2"; shift 2 ;;
        *) shift ;;
    esac
done

if [[ -n "$ROOT" ]]; then
    cd "$ROOT"
fi

result() {
    echo ""
    if [ "$FAILED" -eq 0 ]; then
        echo -e "${GREEN}All checks passed.${NC}"
    else
        echo -e "${RED}Some checks failed.${NC}"
        exit 1
    fi
}
