# About this module

Create a simple quiz out of a webform with webform quiz elements module.

## New quiz elements

### Radios (quiz element)
Radio buttons have a new property "quiz_options" which indicates which options
is correct and to provide a feedback on the selected answer.

### Result (per quiz element)
Rendered element displays result for a specific quiz radio answer.

### Quiz total score element
Rendered element displays score for the quiz.

## New token
New token `[webform:quiz_elements_count]` is added. It displays count of
quiz questions.
To be used on HTML elements to display something like
`Question 1 out [webform:quiz_elements_count]`

## Prerequisites
`webform` is required for this module

## Installing the Webform Quiz Elements Module
Install by running `composer require drupal/webform_quiz_elements` and
enable the module.

## Help and demo
- create a new webform
- add `Radios (quiz element)` elements, make sure `Required` is set to `true`
- add one `Quiz total score` element to display total score
- add same number of `Result (per quiz element)` and link each to a
corresponding `Radios (quiz)`
- by default `Quiz total score` and `Result (per quiz element)` will be
displayed on `DISPLAY_ON_VIEW`
- in form Settings => Confirmation => Confirmation settings => Message set to
`[webform_submission:values:html]` to display results on confirmation page
- it's possible to create multi-page quiz and put `Radios (quiz element)`
on one page and `Result (per quiz element)` on the next page (currently
question and answer cannot be displayed on the same page without postback).

Import `docs/example.yml` to see quiz in action!
## Roadmap
Here's other possible questions to implement:
- Improved UI and `quiz__options` validation
- Display `Result (per quiz element)` after `Radios (quiz element)`
option is selected
- Quiz checkboxes for answers with multiple options
- Ordering question (eg order cities A, B, C, D by population)
- Image quiz - displays an image as a question
- Image quiz answer - displays image as answers (radios)

Vote and send your requests!
