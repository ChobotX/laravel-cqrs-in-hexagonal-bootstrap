# Shared helpers for check scripts — sourced, not executed.
# Caller must set -euo pipefail before sourcing.

GREEN='\033[0;32m' RED='\033[0;31m' DIM='\033[2m' NC='\033[0m'

STEP_DIR=$(mktemp -d)
trap 'rm -rf "$STEP_DIR"' EXIT

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

wave() { echo -e "\n${DIM}── $1 ──${NC}"; }

run_step() {
    local name="$1"; shift
    local log="$STEP_DIR/$name.log"
    if "$@" > "$log" 2>&1; then
        echo -e "  ${GREEN}✓${NC} $name"
        rm -f "$log"
    else
        echo -e "  ${RED}✗${NC} $name"
        echo "$name" >> "$STEP_DIR/_failed_steps"
    fi
    return 0
}

run_group() {
    local group="$1"; shift
    "$@" > "$STEP_DIR/_${group}.out" 2>&1
}

replay_group() {
    local group="$1"
    [[ -f "$STEP_DIR/_${group}.out" ]] && cat "$STEP_DIR/_${group}.out"
    return 0
}

summary() {
    if [[ ! -f "$STEP_DIR/_failed_steps" ]]; then
        echo -e "\n${GREEN}All checks passed.${NC}"
    else
        echo -e "\n${DIM}── Failure details ──${NC}"
        while IFS= read -r name; do
            echo -e "\n${RED}✗${NC} $name"
            sed 's/^/  /' "$STEP_DIR/$name.log"
        done < "$STEP_DIR/_failed_steps"
        echo -e "\n${RED}Some checks failed.${NC}"
        exit 1
    fi
}
