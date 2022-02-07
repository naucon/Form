# Changelog

## 3.0.0 (2022-02-07)

  * add compatibility to Symfony 4, min. requirement PHP 7.1 

## 2.0.2 (2020-11-25)

  * fix behavior of Symfony CSRF Bridge implementation for generating new token
  
## 2.0.1 (2020-02-17)

  * fix issue in email validation with line breaks

## 2.0.0 (2020-02-11)

  * upgrade to symfony components 3.0 (breaking change)

## 1.1.5 (2020-02-17)

  * fix issue in email validation with line breaks

## 1.1.4 (2019-12-11)

  * Make isEmail regexp compatible to RFC2822

## 1.1.3 (2018-06-22)

  * add data attributes to choice radio field.

## 1.1.2 (2018-06-15)

  * fix issue in textarea helper with `value` attribute (introduced in 1.1.1)

## 1.1.1 (2018-06-14)

  * add attribute whitelist (id, class, style) to `formField`, `formChoise` helper.
  * improve docs by adding example of smarty with form collection and `ncform_option`.
  * improve docs by adding example of smarty `ncform_field` with `type="id"`, `type="name"`, `type="value"`.

## 1.1.0 (2018-06-10)

  * breaking change of behavior in validators so that null values are valid (like in symfony).

## 1.0.2 (2018-02-07)

  * add attribute whitelist (id, class, style) to overwrite whitelisted attributes in `formStart` helper.

## 1.0.1 (2017-09-13)

  * add option `COLLECTION_TYPE_ANY`. Binds payload to any matching entity.
  * add php7.1 to travis ci

## 1.0.0 (2017-05-25)

  * Initial release
