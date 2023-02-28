### Setup
1. install docker-compose or podman-compose
2. podman-compose up -d
3. podman exec -it php bash
4. composer install && make test && make db table
5. edit hosts file (e.g. 127.0.0.1 www.apple.com)

### Usage
1. setup
2. visit web site
3. edit rules (/_debug/admin)

### Tips
 - /_debug/view
 - cache rule example: scheme_host -> https://www.apple.com scheme_host_match -> *

### TODO
body edit
