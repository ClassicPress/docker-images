#!/usr/bin/env bash
#
# Point every Dockerfile at a ClassicPress release and pin its checksum.
#
# Usage: ./update-classicpress.sh 2.7.4
#
# Downloads the release tarball from GitHub, computes its SHA-256, and writes
# the version and checksum into apache/*/Dockerfile and fpm-alpine/*/Dockerfile.
# Also updates the version shown in readme.md. Review the diff, then commit.
#
set -Eeuo pipefail

version="${1:-}"
if ! [[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
	echo >&2 "usage: $0 <version>   (for example: $0 2.7.4)"
	exit 1
fi

cd "$(dirname "$0")"

url="https://github.com/ClassicPress/ClassicPress-release/archive/refs/tags/$version.tar.gz"
tmp="$(mktemp)"
trap 'rm -f "$tmp"' EXIT

echo "Downloading $url"
curl -fsSL --retry 5 -o "$tmp" "$url"

# Make sure the tarball has the layout the Dockerfiles expect.
tar -tzf "$tmp" "ClassicPress-release-$version/wp-includes/version.php" > /dev/null

if command -v sha256sum > /dev/null; then
	sha256="$(sha256sum "$tmp" | cut -d' ' -f1)"
else
	sha256="$(shasum -a 256 "$tmp" | cut -d' ' -f1)"
fi
echo "SHA-256: $sha256"

# Portable in-place edit (GNU and BSD sed differ on -i).
edit() {
	local file="$1"; shift
	sed "$@" "$file" > "$file.tmp" && mv "$file.tmp" "$file"
}

shopt -s nullglob
for dockerfile in apache/*/Dockerfile fpm-alpine/*/Dockerfile; do
	edit "$dockerfile" \
		-e "s/^\([[:space:]]*\)version='[0-9.]*'; \\\\\$/\1version='$version'; \\\\/" \
		-e "s/^\([[:space:]]*\)sha256='[^']*'; \\\\\$/\1sha256='$sha256'; \\\\/"
	grep -q "version='$version'" "$dockerfile"
	grep -q "sha256='$sha256'" "$dockerfile"
	echo "Updated $dockerfile"
done

# readme.md: "[2.7.3‑php8.5‑apache]" -> "[2.7.4‑php8.5‑apache]" (the readme uses non-breaking hyphens)
edit readme.md -e "s/\[[0-9][0-9]*\.[0-9][0-9]*\.[0-9][0-9]*\(‑php\)/[$version\1/g"
echo "Updated readme.md"

echo
echo "Done. Review with 'git diff', then commit."
