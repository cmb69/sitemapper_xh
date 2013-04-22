PHPUNIT=phpunit.bat

.PHONY: tests
tests:
	$(PHPUNIT) --colors tests/

.PHONY: coverage
coverage:
	$(PHPUNIT) --coverage-html tests/coverage/ tests/
