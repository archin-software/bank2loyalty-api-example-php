# bank2loyalty-api-example-php

Example project of an implementation of [bank2loyalty-api-php](https://github.com/archin-software/bank2loyalty-api-php)

## Introduction

### HappyFlower example

This project contains an example of a fictive program that can be used for the Bank2Loyalty program, see the `public/`
directory.

Note: Within this project you will find a collection of images used to display on the reader (`resources/`). To start
using the images you have to upload them to the Bank2Loyalty portal and configure the keys.

### Ask consumer for an amount

In the `public/amount/` directory you'll find an example for asking a consumer an amount. The reader will instruct the
user to enter an amount. The entered amount will be returned to the 'Script result' endpoint.

### Ask consumer for a PIN code

In the `public/pin/` directory you'll find an example for asking a consumer a PIN code. The reader will instruct the
user to enter a PIN code. The entered PIN code will be returned to the 'Script result' endpoint.

Note: This will require some additional [security](https://developer.bank2loyalty.com/#47-showenterpin-model).

## Questions?

Want to know more in-depth about Bank2Loyalty? Read
the [developer documentation](https://developer.bank2loyalty.com/#document-history)
