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
PHPSTAN_VERSION="${PHPSTAN_VERSION:-2.1.56}"

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

# A module requiring a newer core (need_dolibarr_version) than the one analysed against is not analysed:
# the symbols it uses do not exist yet in that core, so every report would be noise. It is added to the
# excluded paths through a generated configuration that includes phpstan.neon.dist.
# The version is in version.inc.php (DOL_MAJOR_VERSION, DOL_MINOR_VERSION) from Dolibarr 24, in filefunc.inc.php before.
core_version=""
if [ -f "$DOLIBARR_HTDOCS/version.inc.php" ]; then
	core_major=$(sed -nE "s/.*define\('DOL_MAJOR_VERSION', *'([0-9]+)'.*/\1/p" "$DOLIBARR_HTDOCS/version.inc.php" | head -n 1)
	core_minor=$(sed -nE "s/.*define\('DOL_MINOR_VERSION', *'([0-9]+).*/\1/p" "$DOLIBARR_HTDOCS/version.inc.php" | head -n 1)
	if [ -n "$core_major" ] && [ -n "$core_minor" ]; then
		core_version="$core_major $core_minor"
	fi
fi
if [ -z "$core_version" ]; then
	core_version=$(sed -nE "s/.*define\('DOL_VERSION', *'([0-9]+)\.([0-9]+).*/\1 \2/p" "$DOLIBARR_HTDOCS/filefunc.inc.php" | head -n 1)
fi
if [ -z "$core_version" ]; then
	echo "Version of the core not found: all the modules are analysed" >&2
fi
config_args=()
excluded=()
if [ -n "$core_version" ]; then
	read -r core_major core_minor <<< "$core_version"
	for descriptor in htdocs/*/core/modules/mod*.class.php; do
		need=$(sed -nE 's/.*need_dolibarr_version *= *(array\(|\[) *([0-9]+) *, *([0-9]+).*/\2 \3/p' "$descriptor" | head -n 1)
		[ -z "$need" ] && continue
		read -r need_major need_minor <<< "$need"
		if [ "$need_major" -gt "$core_major" ] || { [ "$need_major" -eq "$core_major" ] && [ "$need_minor" -gt "$core_minor" ]; }; then
			module_dir=${descriptor%%/core/modules/*}
			excluded+=("$module_dir")
			echo "Not analysed: $module_dir requires Dolibarr $need_major.$need_minor, the core is $core_major.$core_minor" >&2
		fi
	done
fi
custom_config=0
for arg in "$@"; do
	case "$arg" in -c|--configuration|--configuration=*) custom_config=1 ;; esac
done
if [ "${#excluded[@]}" -gt 0 ] && [ "$custom_config" -eq 0 ]; then
	generated="$cache_dir/phpstan-excluded-modules.neon"
	{
		printf 'includes:\n\t- %s\n\nparameters:\n\texcludePaths:\n\t\tanalyseAndScan:\n' "$repo_root/phpstan.neon.dist"
		for module_dir in "${excluded[@]}"; do
			printf '\t\t\t- %s/*\n' "$repo_root/$module_dir"
		done
	} > "$generated"
	config_args=(-c "$generated")
fi

exec php -d memory_limit="${PHPSTAN_MEMORY_LIMIT:-4G}" "$phar" analyse "${config_args[@]}" "$@"
