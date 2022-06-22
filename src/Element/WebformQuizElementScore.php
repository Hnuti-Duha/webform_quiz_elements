<?php

namespace Drupal\webform_quiz_elements\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element to display quiz score.
 *
 * @FormElement("webform_quiz_elements_score")
 */
class WebformQuizElementScore extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#quiz_title' => '',
      '#quiz_total_questions_count' => '',
      '#quiz_correct_answers_count' => '',
      '#quiz_score' => '',
      '#quiz_is_pass' => FALSE,
      '#quiz_feedback_message' => '',
      '#theme' => 'webform_quiz_elements_score',
      '#attributes' => [],
      '#attached' => [
        'library' => [
          'webform_quiz_elements/styles',
        ],
      ],
    ];
  }

}
