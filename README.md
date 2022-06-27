# About this module

Create a simple quiz out of a webform with webform quiz elements module.

## New quiz elements

### Radios (quiz element)
Radio buttons have a new property "quiz__options" which indicates which options
is correct and to provide a feedback on the selected answer.

Each radio option must have a quiz option:
- `is_correct` indicates if this option value is a correct answer to the
question
- `feedback` will be displayed to on the result

For example, for a `radios` question 'What is the capital of the UK?' `options`
are defined as:
```
value1: 'London'
value2: 'Paris'
value3: 'Madrid'
```

`quiz__options` might be:
```
value1:
  is_correct: true
  feedback: 'Correct. London is the capital of the UK.'
value2:
  is_correct: false
  feedback: 'Incorrect. Paris is the capital of France, not the UK.'
value3:
  is_correct: false
  feedback: 'Incorrect. Madrid is the capital of Spain, not the UK.'
```

See `docs/example.yml` for full form example in Yaml.

### Result (per quiz element)
Rendered element displays result for a specific quiz radio answer. It relies
on `quiz__options` value of the radios element (see `Radios (quiz element)`
above)

### Quiz total score element
Rendered element displays score for the quiz.
Content editor sets `passing_score_percentage` to calculate if user passed or
failed the quiz. Corresponding message will be displayed to the user.

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
