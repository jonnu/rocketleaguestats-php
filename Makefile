VENDOR_DIR = vendor

help:
	@echo ""
	@echo "Available commands:"
	@echo ""
	@echo "	test     : Run all unit tests"
	@echo "	coverage : Report code coverage"
	@echo ""

test:
	$(VENDOR_DIR)/bin/phpunit

coverage:
	$(VENDOR_DIR)/bin/phpunit --coverage-text

.PHONY:
	help
