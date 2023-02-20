APP_ENV := dev
ARG_ENV := --env=$(APP_ENV)

all: db table data grant

db:
	-./bin/console doctrine:database:drop --force $(ARG_ENV)
	./bin/console doctrine:database:create $(ARG_ENV)

table:
	./bin/console doctrine:schema:create $(ARG_ENV)

data:
	./bin/console doctrine:fixtures:load -n $(ARG_ENV)

grant:
	-chmod 666 var/*.db

test:
	make all APP_ENV=test
	./bin/phpunit

clean:
	-rm var/*.db
