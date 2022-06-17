<?php

namespace Drupal\webform_quiz_elements\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElementBase;
use Drupal\webform\Plugin\WebformElementDisplayOnInterface;
use Drupal\webform\Plugin\WebformElement\WebformDisplayOnTrait;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_quiz_elements\Plugin\WebformQuizElementsInterface;

/**
 * Provides a 'webform_quiz_elements_result' element.
 *
 * @WebformElement(
 *   id = "webform_quiz_elements_result",
 *   label = @Translation("Quiz element result"),
 *   description = @Translation("Provides a container with quiz element result."),
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
      'display_on' => WebformElementDisplayOnInterface::DISPLAY_ON_BOTH,
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
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    parent::prepare($element, $webform_submission);

    // Hide element if it should not be displayed on 'form'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_FORM)) {
      $element['#access'] = FALSE;
    }

    if (isset($element['#source'])) {
      $source = $element['#source'];
      $webform = $webform_submission->getWebform();
      $data = $webform_submission->getElementData($source);

      if (isset($data)) {
        $quiz_element = $webform->getElement($source);
        $answer = $quiz_element['#options'][$data];
        $feedback = isset($quiz_element['#quiz__options']) ? $quiz_element['#quiz__options'][$data] : NULL;

        $element = $element + [
          '#quiz_title' => $quiz_element['#title'],
          '#quiz_answer' => $answer,
          '#quiz_is_correct' => isset($feedback) && isset($feedback['is_correct']) ? $feedback['is_correct'] : FALSE,
          '#quiz_feedback' => isset($feedback) && isset($feedback['feedback']) ? $feedback['feedback'] : FALSE,
        ];
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildHtml(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)) {
      return [];
    }

    if (isset($element['#source'])) {
      $source = $element['#source'];
      $webform = $webform_submission->getWebform();
      $data = $webform_submission->getElementData($source);

      if (isset($data)) {
        $quiz_element = $webform->getElement($source);
        $answer = $quiz_element['#options'][$data];
        $feedback = isset($quiz_element['#quiz__options']) ? $quiz_element['#quiz__options'][$data] : NULL;

        $element = $element + [
          '#quiz_title' => $quiz_element['#title'],
          '#quiz_answer' => $answer,
          '#quiz_is_correct' => isset($feedback) && isset($feedback['is_correct']) ? $feedback['is_correct'] : FALSE,
          '#quiz_feedback' => isset($feedback) && isset($feedback['feedback']) ? $feedback['feedback'] : FALSE,
        ];
      }
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function buildText(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)) {
      return [];
    }

    return ['#plain_text' => 'buildText'];
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedTypes(array $element) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getElementSelectorOptions(array $element) {
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
      '#destination' => $this->t('Please note, the source and destination element must be the same element types.'),
    ];
    $form['quiz_result']['source'] = [
      '#type' => 'select',
      '#title' => $this->t('Quiz element'),
      '#options' => $this->getWebformQuizElementsAsOptions($form_state),
      '#required' => TRUE,
    ];

    return $form;
  }

}
