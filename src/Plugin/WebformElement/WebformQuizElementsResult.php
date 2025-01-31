<?php

namespace Drupal\webform_quiz_elements\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElement\WebformDisplayOnTrait;
use Drupal\webform\Plugin\WebformElementBase;
use Drupal\webform\Plugin\WebformElementDisplayOnInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_quiz_elements\Plugin\WebformQuizElementsInterface;

/**
 * Provides a 'webform_quiz_elements_result' element.
 *
 * @WebformElement(
 *   id = "webform_quiz_elements_result",
 *   label = @Translation("Result (per quiz element)"),
 *   description = @Translation("Provides a container with a result of a quiz element, eg your answer is correct/incorrect and feedback."),
 *   category = @Translation("Quiz elements"),
 * )
 */
class WebformQuizElementsResult extends WebformElementBase implements WebformElementDisplayOnInterface, WebformQuizElementsInterface {
  use WebformDisplayOnTrait;
  use WebformQuizElementsTrait;

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    $properties = [
      'source' => '',
      'display_on' => WebformElementDisplayOnInterface::DISPLAY_ON_VIEW,
    ] + parent::defineDefaultProperties();
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isInput(array $element) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isContainer(array $element) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, ?WebformSubmissionInterface $webform_submission = NULL) {
    parent::prepare($element, $webform_submission);

    // Hide element if it should not be displayed on 'form'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_FORM)) {
      $element['#access'] = FALSE;
    }

    if (isset($element['#source'])) {
      $element += $this->getElementVariables($webform_submission, $element['#source']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildHtml(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)
      || !isset($element['#source'])) {
      return [];
    }

    return $element + $this->getElementVariables($webform_submission, $element['#source']);
  }

  /**
   * {@inheritdoc}
   */
  public function buildText(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)
      || !isset($element['#source'])) {
      return [];
    }

    $source = $element['#source'];
    $webform = $webform_submission->getWebform();
    $answer_data = $webform_submission->getElementData($source);

    if (isset($answer_data)) {
      $quiz_element = $webform->getElement($source);
      $title = $quiz_element['#title'];
      $answer = $quiz_element['#options'][$answer_data];
      $option_feedback = isset($quiz_element['#quiz__options']) ? $quiz_element['#quiz__options'][$answer_data] : NULL;
      $is_correct = isset($option_feedback) && isset($option_feedback['is_correct']) ? 'CORRECT' : 'INCORRECT';
      $feedback = isset($option_feedback) && isset($option_feedback['feedback']) ? $option_feedback['feedback'] : FALSE;

      $text = $this->t('Quiz result: Question: @title Your answer: @answer Result: @is_correct Feedback: @feedback', [
        '@title' => $title,
        '@answer' => $answer,
        '@is_correct' => $is_correct,
        '@feedback' => $feedback,
      ]);
      return ['#plain_text' => $text . PHP_EOL];
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['quiz_result'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Show results for'),
    ];

    $form['quiz_result']['source'] = [
      '#type' => 'select',
      '#title' => $this->t('Quiz element'),
      '#help' => $this->t('Select quiz element for which to show results.'),
      '#options' => $this->getWebformQuizElementsAsOptions($form_state),
      '#required' => TRUE,
    ];

    $form['quiz_result']['display_on'] = [
      '#type' => 'select',
      '#title' => $this->t('Display on'),
      '#options' => $this->getDisplayOnOptions(),
    ];
    return $form;
  }

  /**
   * Get element variables for rendering.
   */
  private function getElementVariables($webform_submission, $source) {
    $answer_data = $webform_submission->getElementData($source);
    $webform = $webform_submission->getWebform();

    if (isset($answer_data) && isset($webform)) {
      $quiz_element = $webform->getElement($source);

      return [
        '#quiz_title' => $quiz_element['#title'],
        '#quiz_options' => $this->getQuizOptionsWithFeedback($quiz_element, $answer_data),
      ];
    }

    return [];
  }

}
