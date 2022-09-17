all:
	@echo hello

db:
	./bin/console doctrine:database:drop --force
	./bin/console doctrine:database:create

