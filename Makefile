all: db table data grant

db:
	./bin/console doctrine:database:drop --force
	./bin/console doctrine:database:create

table:
	./bin/console doctrine:schema:create

data:
	./bin/console doctrine:fixtures:load -n

grant:
	-chmod 666 var/*.db
