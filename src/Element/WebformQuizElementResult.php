<?php

namespace Drupal\webform_quiz_elements\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element for quiz_element_result.
 *
 * @FormElement("webform_quiz_elements_result")
 */
class WebformQuizElementResult extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#quiz_title' => '',
      '#quiz_answer' => '',
      '#quiz_is_correct' => FALSE,
      '#quiz_feedback' => '',
      '#theme' => 'webform_quiz_elements_result',
      '#attributes' => [],
    ];
  }

}
