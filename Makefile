PHPUNIT=phpunit.bat
PHPCI=pci.bat

.PHONY: tests
tests:
	$(PHPUNIT) --colors tests/

.PHONY: coverage
coverage:
	$(PHPUNIT) --coverage-html tests/coverage/ tests/

.PHONY: pci
pci:
	$(PHPCI) -id pcidirs.txt -d .
