##WordPress repository ---------------------------------------------------------
publish: ## Publish a new plugin version to WordPress plugin repository
publish:
	@if [ -d "${WP_MAUTIC_SVN_TRUNK}" ]; \
	then \
		./dist/release.sh --path ${WP_MAUTIC_SVN_TRUNK} --commit; \
	else \
		echo "You must specific WP_MAUTIC_SVN_TRUNK variable to choose destination for published code."; \
		echo "Example:"; \
		echo "  WP_MAUTIC_SVN_TRUNK=../wp-mautic/trunk make publish"; \
	fi
##QA ---------------------------------------------------------------------------
code-sniffer: ## Runs phpcbf & phpcs
code-sniffer: vendor
	./vendor/bin/phpcbf --standard=phpcs.xml
	./vendor/bin/phpcs --standard=phpcs.xml

test-coverage: ## Runs phpunit full tests suite with coverage
test-coverage: vendor phpunit.xml
	./vendor/bin/phpunit --coverage-html ./tests-coverage

test: ## Runs phpunit
test: vendor phpunit.xml
	./vendor/bin/phpunit

# Commons ----------------------------------------------------------------------
vendor: composer.lock
	composer install

phpunit.xml: phpunit.xml.dist
	@if [ -f phpunit.xml ]; \
	then\
		echo '\033[1;41m/!\ The phpunit.xml.dist file has changed. Please check your phpunit.xml file (this message will not be displayed again).\033[0m';\
		touch phpunit.xml;\
		exit 1;\
fi

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
