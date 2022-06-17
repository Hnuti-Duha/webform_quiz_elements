<?php

namespace Drupal\webform_quiz_elements\Plugin;

/**
 * Provides a webform quiz elements interface.
 */
interface WebformQuizElementsInterface {

  /**
   * Includes a list of all quiz elements.
   *
   * @var array
   */
  const QUIZ_ELEMENTS = [
    'quiz_element_radios',
  ];

}
