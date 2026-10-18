#!/usr/bin/env bash
# Runs PHPStan on the modules of this repository against a Dolibarr checkout.
#
# The core is not analysed, only indexed, so any checkout of a supported release works. Point at
# one with DOLIBARR_HTDOCS, or let the script find one in the usual places.
#
#   dev/tools/phpstan/phpstan.sh                                  		# analyse all
#   DOLIBARR_HTDOCS=~/git/dolibarr/htdocs dev/tools/phpstan/phpstan.sh	# analyze all after setting path to Dolibarr core
#   dev/tools/phpstan/phpstan.sh einvoicing/class/document.class.php   	# one file
#
# Any argument is passed on to PHPStan, so --generate-baseline, --error-format and the rest work.
set -euo pipefail

# The version the Dolibarr core runs in its own CI, so both projects report the same things.
PHPSTAN_VERSION="${PHPSTAN_VERSION:-2.1.12}"

repo_root=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../../.." && pwd)
cache_dir="$repo_root/.run-phpstan"
phar="$cache_dir/phpstan-$PHPSTAN_VERSION.phar"

# Where the Dolibarr core is. A checkout of any supported release does, the analysis reads it only
# to know the symbols of the core.
if [ -z "${DOLIBARR_HTDOCS:-}" ]; then
	for candidate in \
		"$repo_root/../dolibarr/htdocs" \
		"$HOME/git/dolibarr/htdocs" \
		"$HOME/dolibarr/htdocs"; do
		if [ -f "$candidate/filefunc.inc.php" ]; then
			DOLIBARR_HTDOCS=$(cd -- "$candidate" && pwd)
			break
		fi
	done
fi
if [ -z "${DOLIBARR_HTDOCS:-}" ] || [ ! -f "$DOLIBARR_HTDOCS/filefunc.inc.php" ]; then
	echo "No Dolibarr core found. Set DOLIBARR_HTDOCS to the htdocs directory of a checkout:" >&2
	echo "  DOLIBARR_HTDOCS=/path/to/dolibarr/htdocs $0" >&2
	exit 2
fi
export DOLIBARR_HTDOCS

# PHPStan itself. The phar is pinned and cached next to the analysis cache, both out of git.
mkdir -p "$cache_dir"
if [ ! -f "$phar" ]; then
	echo "Fetching PHPStan $PHPSTAN_VERSION..." >&2
	curl -sSfL -o "$phar.tmp" \
		"https://github.com/phpstan/phpstan/releases/download/$PHPSTAN_VERSION/phpstan.phar"
	mv "$phar.tmp" "$phar"
fi

echo "PHPStan $PHPSTAN_VERSION on $DOLIBARR_HTDOCS" >&2

cd "$repo_root"
exec php -d memory_limit="${PHPSTAN_MEMORY_LIMIT:-4G}" "$phar" analyse "$@"
