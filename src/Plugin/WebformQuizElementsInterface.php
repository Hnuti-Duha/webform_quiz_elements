<?php

namespace Drupal\webform_quiz_elements\Plugin;

/**
 * Provides a webform quiz elements interface.
 */
interface WebformQuizElementsInterface {

  /**
   * Includes a list of quiz (questions) elements.
   *
   * @var array
   */
  const QUIZ_ELEMENTS = [
    'quiz_element_radios',
  ];

  /**
   * Includes a list of quiz rendered elements (results, score).
   *
   * @var array
   */
  const QUIZ_RENDERED_ELEMENTS = [
    'webform_quiz_elements_score',
    'webform_quiz_elements_result',
  ];

}
