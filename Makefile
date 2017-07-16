release: check-tag
release:
	cat src/Core/composer.json | jq --indent 4 -r '.extra.uiVersion = "$(tag)"' | tee src/Core/composer.json > /dev/null
	cat ui/package.json | jq -r '.version = "$(tag)"' | tee ui/package.json > /dev/null
	git add src/Core/composer.json ui/package.json
	git commit -m "Tagging for $(tag) release"
	git tag "$(tag)"

check-tag:
ifndef tag
	$(error Tag is not defined.  Define it like: tag=1.0.0)
endif