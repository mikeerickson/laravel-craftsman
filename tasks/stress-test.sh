#!/bin/bash

ESC_SEQ="\x1b["
COL_RESET=${ESC_SEQ}"39;49;00m"
COL_LCYAN=${ESC_SEQ}"36;01m"

ITERATIONS=${1:-5}

for i in `seq 1 "$ITERATIONS"`; do
  echo ""
  printf "${COL_LCYAN}==> Running Stress Test: Iteration ${i} of ${ITERATIONS}...${COL_RESET}\n"
  vendor/bin/phpunit -c phpunit.ci.xml
done
