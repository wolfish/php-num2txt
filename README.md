# What is this?
This is a basic CLI application based on Symfony components, that provides
command for converting number to text representation.

Current limits are:
- Only non-negative numbers
- Only numbers below 1 000 000 000
- Only two digits decimal precision .00

It supports multiple languages, with possibility to add more.

Currently supported languages are:
- English (default)
- Polish

# How to use this?
1. Clone this repository
2. Run `composer install`
3. Use it as standard Symfony console:
- To display help:
`php app.php num2txt --help`
- To try it:
`php app.php num2txt 1234.44`
- To try it with different language:
`php app.php num2txt 4321.11 pl`

*(available languages are in `/lang` directory)*

# How to test it?
You can test it with phpunit using command tests prepared in `tests/` directory.

Just run `./bin/phpunit` or `php bin/phpunit` on Windows
