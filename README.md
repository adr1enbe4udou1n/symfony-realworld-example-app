# ![RealWorld Example App](logo.png)

Symfony codebase containing real world examples (CRUD, auth, advanced patterns, etc) that adheres to the [RealWorld](https://github.com/gothinkster/realworld-example-apps) spec and API.

![CI](https://gitea.okami101.io/conduit/symfony/actions/workflows/build.yaml/badge.svg)

## [RealWorld](https://github.com/gothinkster/realworld)

This codebase was created to demonstrate a fully fledged fullstack application built with Symfony including CRUD operations, authentication, routing, pagination, and more.

We've gone to great lengths to adhere to the Symfony & PHP community styleguides & best practices.

For more information on how to this works with other frontends/backends, head over to the [RealWorld](https://github.com/gothinkster/realworld) repo.

See [my works](https://blog.okami101.io/works/) for my personal Backend & Frontend RealWorld apps collection.

## Usage

### PostgreSQL

This project use **PostgreSQL** as main database provider. You can run it easily via `docker-compose up -d`.

Two databases will spin up, one for normal development and one dedicated for integrations tests.

### Run app

```sh
composer install
php bin/console lexik:jwt:generate-keypair # generate keys for jwt auth
php bin/console d:m:m # migrate main database
php bin/console d:m:m --env=test # migrate test database
php bin/console h:f:l # seed alice fake data
php -S localhost:8000 -t public
```

And that's all, go to <http://localhost:8000/api> for full swagger documentation

### Validate API with Newman

Launch follow scripts for validating realworld schema, note as **you must start with purged database** before run next commands :

```sh
php -S localhost:8000 -t public
npx newman run postman.json --global-var "APIURL=http://localhost:8000/api" --global-var="USERNAME=johndoe" --global-var="EMAIL=john.doe@example.com" --global-var="PASSWORD=password"
```

### Full test suite

This project is fully tested via PHPUnit, just run `php bin/phpunit` for launching it.

## License

This project is open-sourced software licensed under the [MIT license](https://adr1enbe4udou1n.mit-license.org).
