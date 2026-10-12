# PHPStan

PHPStan analyses the module code of this repository. It does not replace [phan](../phan/README.md),
which stays the check the `dolibarr-community-modules` ecosystem expects: the two tools report
different things and both have to pass.

## What it analyses, and against what

Only the module is analysed. The Dolibarr core is **indexed, not analysed**: `scanDirectories` in
`../../../phpstan.neon.dist` points at a core checkout so PHPStan knows its classes, methods,
properties and functions, and reports nothing about them.

Which checkout does not matter for correctness, but it does matter for what is found: the module
supports Dolibarr 18 to 24, whose cores do not offer the same API. Analysing against several of
them is what catches a call written for a recent core on a page an older one still serves —
`recordNotFound()`, which arrived in Dolibarr 20, was found that way.

`bootstrap.php` defines the handful of constants (`DOL_DOCUMENT_ROOT` and friends) that a module
resolves its `require_once` against. It deliberately does not load `master.inc.php` or
`main.inc.php`: those open a database, start a session and read a `conf.php` that does not exist
here, and PHPStan needs none of it to know the symbols.

`DOL_VERSION` is left undefined on purpose, and listed in `dynamicConstantNames`. A module that
supports seven cores guards its calls with version tests; pinning the constant to the version of
the checkout under analysis would let PHPStan call every one of those guards dead code.

## Running it

```bash
# with a core checkout in one of the usual places (../dolibarr, ~/git/dolibarr)
dev/tools/phpstan/phpstan.sh

# or say where it is
DOLIBARR_HTDOCS=/path/to/dolibarr/htdocs dev/tools/phpstan/phpstan.sh

# one file, or any other PHPStan argument
dev/tools/phpstan/phpstan.sh einvoicing/class/document.class.php
```

The script pins PHPStan 2.1.12, the version the Dolibarr core runs in its own CI, and caches the
phar and the analysis cache in `.run-phpstan/`, which git ignores. Nothing to install.

## Level: the posture of the core, copied

`phpstan.neon.dist` carries the settings block of the core's own `phpstan.neon.dist`, as is: level
10, `customRulesetUsed: true`, and the families of checks the hand written PHPDoc of Dolibarr cannot
honour turned off - `checkNullables`, `checkUnionTypes`, `reportMaybes`, `treatPhpDocTypesAsCertain`
and the rest. Reading that as a plain level 10 would be wrong; reading it as lax would be wrong too.
Measured on this module, against Dolibarr 24:

| posture | reports |
| --- | --- |
| strict level 5 | 295 |
| **the core's block, level 10** | **352** |
| strict level 6 | 450 |

Two reasons for taking the core's dials rather than picking our own. A contributor who knows the CI
of Dolibarr finds here exactly what he knows. And what the core judges worth reporting is what is
reported here, no more and no less - the argument holds by itself in review.

## The two baselines

`phpstan.neon.dist` loads both, always:

| file | what it holds |
| --- | --- |
| `baseline.neon` | what the newest supported core reports (299 entries) |
| `baseline-legacy.neon` | what the older cores add on top of that (350 entries) |

For scale: the core carries 1 527 entries for its own 1,4 million lines.

Together they make every core of the matrix green, so a new error on any of them is reported. They
are the entry price of introducing the tool on an existing code base, not a place to park new
findings: **an error a change introduces is fixed, or documented where it happens with a
`@phpstan-ignore` carrying the reason — never by a line added to a baseline.** That is the same
rule the phan baseline follows.

Refresh them after a batch of fixes lands, never inside one — two branches editing a baseline
conflict on every line:

```bash
DOLIBARR_GIT=~/git/dolibarr dev/tools/phpstan/refresh-baseline.sh
```

## What is ignored, and why

Everything in `ignoreErrors` in `phpstan.neon.dist` carries its reason next to it. In short:

- the `main.inc.php` / `master.inc.php` ladder every external module page and script opens with: it
  resolves at runtime from inside an instance, never from this repository;
- the string expressions phan reads its inline type assertions from;
- `ActionsMulticompany` and `DaoMulticompany`, of the separate non free Multicompany module, reached
  only behind `isModEnabled('multicompany')`;
- the private and protected calls in `lib/buildinvoicelines.inc.php`, which is included from inside
  a method and runs with that `$this` — phan is told the same thing by the `@phan-file-suppress`
  the file already carries;
- the core file of a version gate whose other branch is the module's own backport;
- the case of the TCPDF setters, which the bundled library renamed between Dolibarr 18 and 24, so
  no spelling satisfies both.

Where a type can be written instead, it is written: each `'@phan-var-force X $y'` assertion the
module already carried now has a `/** @var X $y */` next to it, which is the form PHPStan reads.

## CI

`.github/workflows/phpstan.yml`. A pull request is analysed against the two ends of the supported
range, what lands on `main` against all seven cores. No database, no instance: a checkout of the
core and the phar.
