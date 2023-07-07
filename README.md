# Ebanx Test API
> An API to the ebanx software engineer test

## Installation

You should have Docker installed and running on your system.

```sh
sudo sh docker_setup.sh && ./vendor/bin/sail up
```

## Usage example

To set up the containers, we are using Laravel Sail. Simply run ```./vendor/bin/sail up```, and everything will be okay.
The container will set up a server accessible at http://localhost:80.

## Testing

The application has automated tests. To test if everything is working correctly, simply run the following command:

```sh
./vendor/bin/sail artisan test
```

## How to reach me

Dear tech reviewer, if you liked what you see, you can contact me via e-mail – [wellingtonbarbosa.dev@gmail.com](mailto:wellingtonbarbosa@gmail.com) or Instagram - [@owellcarneiro](https://instagram.com/owellcarneiro). My profile is public.

## Contributing

1. Fork it (<https://github.com/wellingtoncarneirobarbosa/ebanx-test/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request
