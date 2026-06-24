#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

WITH_AUDIT=false

for arg in "$@"; do
    case "$arg" in
        --with-audit)
            WITH_AUDIT=true
            ;;
        -h|--help)
            printf 'Usage: %s [--with-audit]\n' "$(basename "$0")"
            printf '\nRuns local project tests and quality gates. Use --with-audit when network access is available.\n'
            exit 0
            ;;
        *)
            printf 'Unknown option: %s\n' "$arg" >&2
            printf 'Usage: %s [--with-audit]\n' "$(basename "$0")" >&2
            exit 2
            ;;
    esac
done

run_step() {
    local title="$1"
    shift

    printf '\n==> %s\n' "$title"
    "$@"
}

run_step "Validate Composer configuration" composer validate --strict
run_step "Run PHP quality gate" composer quality
run_step "Run Node quality gate" npm run quality

if [[ "$WITH_AUDIT" == true ]]; then
    run_step "Audit PHP dependencies" composer audit --locked
    run_step "Audit Node dependencies" npm audit --audit-level=moderate
else
    printf '\nSkipping dependency audits. Re-run with --with-audit when network access is available.\n'
fi

printf '\nAll project tests and quality gates passed.\n'
