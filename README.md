# Barebone Core

MVC framework for building PHP web applications

## Goals

- Have a simple installation process: download and go
- Be open, easy to understand and easy to extend
- Supporting and encouraging best practices
- Provide a solid foundation for almost anything
- Be minimal, but with a good minimum

## Documentation

Some folders may have additional readme files.

## Status

This is work in progress. Tests are on the way and the API may change (slightly).
At this point i don't expect any drastic changes anymore.

## Contributing

Any PR is welcome. If you find issues, please report.

https://github.com/barebone-php/barebone-core

## Testing

Install all required packages with:

    $ npm install

You can run tests with `phpunit` directly or use the provided grunt-task, which
will also run tests whenever a PHP file changes.

The following uses scripts from packages.json and references the binaries 
directly from '/vendor/'

    $ npm start    runs phpunit and starts grunt file watcher
    $ npm test     runs phpunit
    
Alternatives:

    $ phpunit      if installed globally on your system
    $ grunt        if installed globally on your system
    
