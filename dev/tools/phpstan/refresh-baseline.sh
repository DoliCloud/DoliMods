#!/usr/bin/env bash
# Rebuilds the two PHPStan baselines from scratch, over every core the module supports.
#
#   DOLIBARR_GIT=~/git/dolibarr dev/tools/phpstan/refresh-baseline.sh
#
# baseline.neon gets what the newest core reports. baseline-legacy.neon gets, core by core from the
# oldest up, only what that core adds on top of everything already listed: an error seen on several
# cores is written once. Both files are loaded by phpstan.neon.dist, so every core stays green and
# a new error on any of them is reported.
#
# Refresh it after a batch of fixes lands, never inside one: two branches editing the same baseline
# conflict on every line.
set -euo pipefail

# The cores the module supports, oldest first, newest last. Same list as the PHPUnit matrix.
CORES=(18.0.0 19.0.0 20.0.0 21.0.0 22.0.0 23.0.0 24.0.0)

repo_root=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../../.." && pwd)
here="$repo_root/dev/tools/phpstan"
newest="${CORES[${#CORES[@]}-1]}"

if [ -z "${DOLIBARR_GIT:-}" ] || ! git -C "$DOLIBARR_GIT" rev-parse --git-dir >/dev/null 2>&1; then
	echo "Set DOLIBARR_GIT to a clone of Dolibarr/dolibarr holding the release tags." >&2
	exit 2
fi

work=$(mktemp -d)
trap 'rm -rf "$work"' EXIT

checkout() { # tag -> prints the htdocs of a fresh extraction
	rm -rf "${work:?}/core"
	mkdir -p "$work/core"
	git -C "$DOLIBARR_GIT" archive "$1" htdocs | tar -x -C "$work/core"
	echo "$work/core/htdocs"
}

# An empty baseline has to be in place first: --generate-baseline writes what is reported, so a
# file still listing yesterday's errors would produce an empty one.
empty() { printf 'parameters:\n\tignoreErrors: []\n' > "$1"; }
empty "$here/baseline.neon"
empty "$here/baseline-legacy.neon"

echo "== $newest -> baseline.neon"
DOLIBARR_HTDOCS=$(checkout "$newest") "$here/phpstan.sh" \
	--generate-baseline="$here/baseline.neon" --allow-empty-baseline --no-progress

for tag in "${CORES[@]}"; do
	[ "$tag" = "$newest" ] && continue
	echo "== $tag -> baseline-legacy.neon"
	tmp="$work/add.neon"
	DOLIBARR_HTDOCS=$(checkout "$tag") "$here/phpstan.sh" \
		--generate-baseline="$tmp" --allow-empty-baseline --no-progress
	python3 - "$here/baseline-legacy.neon" "$tmp" "$repo_root" <<'PY'
import sys
legacy, added, root = sys.argv[1], sys.argv[2], sys.argv[3]
new = open(added).read()
cut = new.find('ignoreErrors:')
body = new[new.find('\n', cut) + 1:].rstrip('\n') if cut >= 0 else ''
# --generate-baseline writes paths relative to the file it writes; here it wrote to a temp dir.
body = body.replace('path: ' + root + '/', 'path: ../../../')
if not body.strip():
    sys.exit(0)
cur = open(legacy).read().replace('ignoreErrors: []', 'ignoreErrors:')
open(legacy, 'w').write(cur.rstrip('\n') + '\n' + body + '\n')
PY
done

echo
echo "baseline.neon        $(grep -c 'message:' "$here/baseline.neon") entries"
echo "baseline-legacy.neon $(grep -c 'message:' "$here/baseline-legacy.neon") entries"
