PHPUNIT=phpunit.bat

.PHONY: tests
tests:
	$(PHPUNIT) --colors tests/
