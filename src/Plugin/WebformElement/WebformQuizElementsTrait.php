<?php

namespace Drupal\webform_quiz_elements\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_quiz_elements\Plugin\WebformQuizElementsInterface;

/**
 * Provides an 'webform quiz elements' trait.
 */
trait WebformQuizElementsTrait {

  /**
   * Desc.
   */
  private function getWebformQuizElementsCount(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $options = $this->getWebformQuizElementsAsOptions($form_state, $webform_submission);
    $elements = array_merge(...array_values($options));
    return count($elements);
  }

  /**
   * Desc.
   */
  private function getWebformQuizElementsAsOptions(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $webform = NULL;

    if (isset($form_state)) {
      /** @var \Drupal\webform\WebformInterface $webform */
      $webform = $form_state->getFormObject()->getWebform();
    }
    elseif (isset($webform_submission)) {
      $webform = $webform_submission->getWebform();
    }
    else {
      return [];
    }

    $flattened_elements = $webform->getElementsInitializedFlattenedAndHasValue();
    $options = [];
    foreach ($flattened_elements as $element_key => $element) {
      $element_plugin = $this->elementManager->getElementInstance($element);
      if (in_array($element_plugin->getPluginId(), WebformQuizElementsInterface::QUIZ_ELEMENTS)) {
        $options[(string) $element_plugin->getPluginLabel()][$element_key] = $element['#admin_title'];
      }
    }
    ksort($options);

    return $options;
  }

  /**
   * Desc.
   */
  private function getWebformQuizCorrectAnswersCount(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $options = $this->getWebformQuizElementsAsOptions($form_state, $webform_submission);
    $elements = array_merge(...array_values($options));
    return count($elements) - 1;
    // @todo iterate all questions and answers to count correct answers.
  }

  /**
   * Desc.
   */
  private function getWebformQuizScore(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $total = $this->getWebformQuizElementsCount($form_state, $webform_submission);
    $correct = $this->getWebformQuizCorrectAnswersCount($form_state, $webform_submission);

    return $total > 0 ? $correct / $total * 100 : 0;
  }

  /**
   * Desc.
   */
  private function getWebformQuizPass(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $score = $this->getWebformQuizScore($form_state, $webform_submission);
    // @todo get passing score from score element.
    $passing_score = 88;

    return $score >= $passing_score;
  }

}
