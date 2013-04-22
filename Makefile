PHPUNIT=phpunit.bat
PHPCI=pci.bat
SVN=svn

.PHONY: tests
tests:
	$(PHPUNIT) --colors tests/

.PHONY: coverage
coverage:
	$(PHPUNIT) --coverage-html tests/coverage/ tests/

.PHONY: pci
pci:
	$(PHPCI) -id pcidirs.txt -d .

.PHONY: export
export:
	$(SVN) export -q . sitemapper/
	rm -rf sitemapper/sitemapper.komodoproject sitemapper/Makefile sitemapper/pcidirs.txt sitemapper/tests/
	cp sitemapper/config/config.php sitemapper/config/defaultconfig.php
	cp sitemapper/languages/en.php sitemapper/languages/default.php
