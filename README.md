# Symfony forms for concrete5

This is a composer package that provides the possibility to use [Symfony forms](https://symfony.com/doc/current/book/forms.html)
within concrete5. The advantages are obvious if you have read anything about them but to list a few:

- Automatic form tags generation based on the defined fields
- Automatic CSRF validation
- Automatic field errors based on the defined validators
- Automatic saving of forms directly into Doctrine entities
- Less things to worry about in your controller logic
- Streamlining your development pipeline with single pages
- Making developing single page views with forms a lot faster
- Less bugs in your code due to the nature of generalized and well tested components

## How to use?

An example implementation on how to use this is available here:

[https://github.com/mainio/c5_symfony_forms_example](https://github.com/mainio/c5_symfony_forms_example)

## Roadmap

- Make the templates work in themes
- Make the templates work in block views

## License

Licensed under the MIT license. See LICENSE for more information.

Copyright (c) 2015 Mainio Tech Ltd.