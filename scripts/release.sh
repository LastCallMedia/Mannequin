#!/bin/bash

tag=$1

commit=true
tag=""

showhelp () {
  echo "Usage:  release.sh [--no-commit] <tag>"
}

while [ ! $# -eq 0 ]; do
	case "$1" in
		--help | -h)
			showhelp
			exit
			;;
		--no-commit | -n)
			commit=false
			;;
    *)
      tag=$1
	esac
	shift
done

# Validate that the tag has been passed.
if [ -z "$tag" ]; then
  echo "Pass a tag as the first argument"
  exit 1
fi

# Check that the tag matches 1.1.1 format
if ! [[ "$tag" =~ ^[0-9]+.[0-9]+.[0-9]+ ]]; then
  echo "$tag does not match semver format (ex: 1.0.0 or 1.0.0-dev)"
  exit 1
fi

# Check that the tag does not exist yet.
if git rev-parse "$tag" >& /dev/null; then
  echo "Tag $tag already exists"
  exit 1
fi

# Verify that these files are otherwise clean.
if ! git diff-index --quiet HEAD -- ui/package.json src/Core/composer.json; then
  echo "Cannot release with uncommitted changes to package.json or composer.json"
  exit 1
fi

cat src/Core/composer.json | jq --indent 4 -r ".extra.uiVersion = \"$tag\"" | tee src/Core/composer.json > /dev/null
cat ui/package.json | jq -r ".version = \"$tag\"" | tee ui/package.json > /dev/null


if $commit; then
  echo "Committing..."
  git add src/Core/composer.json ui/package.json
  git commit -m "Tagging for $tag release"
  git tag "$tag"
fi
