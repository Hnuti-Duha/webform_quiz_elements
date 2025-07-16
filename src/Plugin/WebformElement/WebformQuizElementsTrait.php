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
   * Returns count of quiz elements.
   */
  private function getWebformQuizElementsCount(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $options = $this->getWebformQuizElementsAsOptions($form_state, $webform_submission);
    $elements = array_merge(...array_values($options));
    return count($elements);
  }

  /**
   * Returns quiz elements as options.
   */
  private function getWebformQuizElementsAsOptions(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $webform = $this->getWebformObject($form_state, $webform_submission);
    if (!isset($webform)) {
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
   * Returns count of correctly answered questions.
   */
  private function getWebformQuizCorrectAnswersCount(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $webform = $this->getWebformObject($form_state, $webform_submission);
    if (!isset($webform)) {
      return [];
    }

    $flattened_elements = $webform->getElementsInitializedFlattenedAndHasValue();
    $count = 0;
    foreach ($flattened_elements as $element_key => $element) {
      $element_plugin = $this->elementManager->getElementInstance($element);
      if (in_array($element_plugin->getPluginId(), WebformQuizElementsInterface::QUIZ_ELEMENTS)) {
        $answer = $webform_submission->getElementData($element_key);
        if (isset($answer)
          && array_key_exists('#quiz__options', $element)
          && $element['#quiz__options'][$answer]['is_correct']) {
          $count++;
        }
      }
    }
    return $count;
  }

  /**
   * Returns total quiz score in percentage.
   */
  private function getWebformQuizScore(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    $total = $this->getWebformQuizElementsCount($form_state, $webform_submission);
    $correct = $this->getWebformQuizCorrectAnswersCount($form_state, $webform_submission);

    return $total > 0 ? $correct / $total * 100 : 0;
  }

  /**
   * Returns quiz webform title.
   */
  private function getWebformQuizTitle(FormStateInterface $form_state = NULL, WebformSubmissionInterface $webform_submission = NULL) {
    /** @var \Drupal\webform\WebformInterface $webform */
    $webform = $this->getWebformObject($form_state, $webform_submission);
    if (!isset($webform)) {
      return '';
    }
    return $webform->get('title');
  }

  /**
   * Returns quiz element options with feedback.
   */
  private function getQuizOptionsWithFeedback($quiz_element, $answer_data) {
    $quiz_options = [];

    if (!(isset($quiz_options)) || !isset($answer_data)) {
      return [];
    }

    foreach ($quiz_element['#options'] as $key => $option) {
      $feedback = (array_key_exists('#quiz__options', $quiz_element)
        && isset($quiz_element['#quiz__options']))
        ? $quiz_element['#quiz__options'][$key] : NULL;
      $quiz_options[] = [
        'key' => $key,
        'option' => $option,
        'is_selected' => ($key == $answer_data),
        'is_correct' => (isset($feedback) && array_key_exists('is_correct', $feedback)
          && $feedback['is_correct']),
        'feedback' => (isset($feedback)
          && array_key_exists('feedback', $feedback)
          ? $feedback['feedback'] : ''),
      ];
    }

    return $quiz_options;
  }

  /**
   * Returns webform object.
   */
  private function getWebformObject($form_state, $webform_submission) {
    $webform = $this->getWebform();
    if (isset($webform)) {
      return $webform;
    }
    elseif (isset($form_state)) {
      /** @var \Drupal\webform\WebformInterface $webform */
      return $form_state->getFormObject()->getWebform();
    }
    elseif (isset($webform_submission)) {
      return $webform_submission->getWebform();
    }
    else {
      return NULL;
    }
  }

}
