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
 *   label = @Translation("Radios (quiz)"),
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
  public function setDefaultValue(array &$element) {
    // Unset empty string as default option to prevent '' === '0' issue.
    // @see \Drupal\Core\Render\Element\Radio::preRenderRadio
    if (
      isset($element['#default_value'])
      && $element['#default_value'] === ''
      && !isset($element['#options'][$element['#default_value']])
    ) {
      unset($element['#default_value']);
    }
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
      '#mode' => 'yaml',
      '#title' => $this->t('Quiz options'),
      '#description' => $this->t("Quiz options"),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);

    // Make sure no blank options get submitted. If they are, just remove them.
    // $values = $form_state->getValues();
    // foreach ($values['options'] as $key => $value) {
    //   if (empty($value)) {
    //     unset($values['options'][$value]);
    //   }
    // }
    // $form_state->setValues($values);
  }

}
