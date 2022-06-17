<?php

namespace Drupal\webform_quiz_elements\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElementBase;
use Drupal\webform\Plugin\WebformElementDisplayOnInterface;
use Drupal\webform\Plugin\WebformElement\WebformDisplayOnTrait;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_quiz_elements\Plugin\WebformQuizElementsInterface;

/**
 * Provides a 'webform_quiz_elements_score' element.
 *
 * @WebformElement(
 *   id = "webform_quiz_elements_score",
 *   label = @Translation("Quiz score"),
 *   description = @Translation("Provides a container with quiz score."),
 *   category = @Translation("Quiz elements"),
 * )
 */
class WebformQuizElementsScore extends WebformElementBase implements WebformElementDisplayOnInterface, WebformQuizElementsInterface {
  use WebformDisplayOnTrait;
  use WebformQuizElementsTrait;

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    $properties = [
      'passing_score_percentage' => 100,
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
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    parent::prepare($element, $webform_submission);

    // Hide element if it should not be displayed on 'form'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_FORM)) {
      $element['#access'] = FALSE;
    }

    $element = $element + [
      '#quiz_total_questions_count' => $this->getWebformQuizElementsCount(NULL, $webform_submission),
      '#quiz_correct_answers_count' => $this->getWebformQuizCorrectAnswersCount(NULL, $webform_submission),
      '#quiz_score' => $this->getWebformQuizScore(NULL, $webform_submission),
      '#quiz_is_pass' => $this->getWebformQuizPass(NULL, $webform_submission),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildHtml(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)) {
      return [];
    }

    $element = $element + [
      '#quiz_total_questions_count' => $this->getWebformQuizElementsCount(NULL, $webform_submission),
      '#quiz_correct_answers_count' => $this->getWebformQuizCorrectAnswersCount(NULL, $webform_submission),
      '#quiz_score' => $this->getWebformQuizScore(NULL, $webform_submission),
      '#quiz_is_pass' => $this->getWebformQuizPass(NULL, $webform_submission),
    ];

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

    $question_count = $this->getWebformQuizElementsCount(NULL, $webform_submission);
    $correct_answers = $this->getWebformQuizCorrectAnswersCount(NULL, $webform_submission);
    $is_pass = $this->getWebformQuizPass(NULL, $webform_submission);
    $text = "You scored $correct_answers out of $question_count. ";
    if ($is_pass) {
      $text .= "Congratulations, you have passed the quiz.";
    }
    else {
      $text .= "Unfortunately, you have not passed the quiz.";
    }

    return ['#plain_text' => $text];
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

    $form['quiz_score'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Quiz score settings'),
      '#destination' => $this->t('Properties'),
    ];
    $form['quiz_score']['passing_score_percentage'] = [
      '#type' => 'number',
      '#min' => 1,
      '#max' => 100,
      '#title' => $this->t('Passing score percentage'),
      '#required' => TRUE,
    ];
    $form['quiz_score']['display_on'] = [
      '#type' => 'select',
      '#title' => $this->t('Display on'),
      '#options' => $this->getDisplayOnOptions(),
    ];

    return $form;
  }

}
