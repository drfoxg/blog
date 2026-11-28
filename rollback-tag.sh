#!/usr/bin/env bash

# Скрипт предполагает, что первая запись журнала — baseline, а последующие записи содержат успешные деплои.
# Он возвращает предыдущий отличающийся тег, а не просто предпоследнюю строку.
# Поэтому повторный деплой одной и той же версии не испортит определение предыдущей версии.

set -euo pipefail

# ============================================================
# Configuration
# ============================================================

LESSONS_BLOG_DIR="$HOME/lessons-blog"
DEPLOY_LOG="$LESSONS_BLOG_DIR/production-deploys.log"

# ============================================================
# Main
# ============================================================

if [[ ! -f "$DEPLOY_LOG" ]]; then
    echo "ERROR: Deployment log not found: $DEPLOY_LOG" >&2
    exit 1
fi

PREVIOUS_TAG=$(
    awk -F '\t' '
        NR == 1 {
            current = $2
            next
        }

        $2 != current {
            previous = current
            current = $2
        }

        END {
            if (previous != "") {
                print previous
            }
        }
    ' "$DEPLOY_LOG"
)

if [[ -z "$PREVIOUS_TAG" ]]; then
    echo "ERROR: Previous deployment tag not found." >&2
    exit 1
fi

printf '%s\n' "$PREVIOUS_TAG"
