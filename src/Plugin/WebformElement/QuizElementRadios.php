<?php

namespace Drupal\webform_quiz_elements\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElement\Radios;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Webform quiz element for radio buttons.
 *
 * @WebformElement(
 *   id = "quiz_element_radios",
 *   api = "https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Render!Element!Radios.php/class/Radios",
 *   label = @Translation("Radios (quiz element)"),
 *   description = @Translation("Provides a form element for a set of radio buttons with a correct answer and feedback."),
 *   category = @Translation("Quiz elements"),
 * )
 */
class QuizElementRadios extends Radios {

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    return [
      'quiz__options' => [],
    ] + parent::defineDefaultProperties();
  }

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    // Render quiz element like a regular radio buttons.
    $element['#type'] = 'radios';
    parent::prepare($element, $webform_submission);

    // Process custom options properties.
    if ($this->hasProperty('quiz__options')) {
      // Unset #options__properties that are not array to prevent errors.
      if (
        isset($element['#quiz__options'])
        && !is_array($element['#quiz__options'])
      ) {
        unset($element['#quiz__options']);
      }
      $this->setElementDefaultCallback($element, 'process');
      $element['#process'][] = [get_class($this), 'processQuizOptions'];
    }
  }

  /**
   * Processes options (custom) properties.
   */
  public static function processQuizOptions(&$element, FormStateInterface $form_state, &$complete_form) {
    if (empty($element['#quiz__options'])) {
      return $element;
    }

    foreach ($element['#quiz__options'] as $option_key => $quiz__options) {
      if (!isset($element[$option_key]) || !is_array($quiz__options)) {
        continue;
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHtml(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function defineTranslatableProperties() {
    return array_merge(
      parent::defineTranslatableProperties(),
      ['quiz__options']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['element']['quiz__options'] = [
      '#type' => 'webform_codemirror',
      '#required' => TRUE,
      '#mode' => 'yaml',
      '#title' => $this->t('Quiz options'),
      '#help' => $this->t("Quiz options in yaml format. <br/>Each radio option
        must have a quiz option. <br/>Only one option can be marked as correct."),
      '#description' => $this->t("Quiz options in yaml format. Each radio option
        must have a quiz option. Only one option can be marked as correct: <br/>
        `is_correct` indicates if this option value is a correct answer, <br/>
        `feedback` will be displayed to on result, for example: <br/><em>
        value1:<br/>&nbsp;&nbsp;is_correct: true<br/>&nbsp;&nbsp;feedback:
        'This answer is correct!'<br/>
        value2:<br/>&nbsp;&nbsp;is_correct: false<br/>&nbsp;&nbsp;feedback:
        'This answer is incorrect!'<br/></em>To validate your YAML use validator,
        eg <a href='https://yamlchecker.com/' target='_blank' rel='noreferrer'>
        https://yamlchecker.com/</a>"),
    ];

    return $form;
  }

}
