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
 *   label = @Translation("Quiz total score"),
 *   description = @Translation("Provides a container with total quiz score."),
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
      'feedback_message_pass' => '',
      'feedback_message_fail' => '',
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

    $element += $this->getElementVariables($element, $webform_submission);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHtml(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)) {
      return [];
    }

    return $element + $this->getElementVariables($element, $webform_submission);
  }

  /**
   * {@inheritdoc}
   */
  protected function defineTranslatableProperties() {
    return array_merge(
      parent::defineTranslatableProperties(),
      ['feedback_message_pass'],
      ['feedback_message_fail']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildText(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    // Hide element if it should not be displayed on 'view'.
    if (!$this->isDisplayOn($element, WebformElementDisplayOnInterface::DISPLAY_ON_VIEW)) {
      return [];
    }

    $quiz_title = $this->getWebformQuizTitle(NULL, $webform_submission);
    $question_count = $this->getWebformQuizElementsCount(NULL, $webform_submission);
    $correct_answers = $this->getWebformQuizCorrectAnswersCount(NULL, $webform_submission);
    $is_pass = $this->getWebformQuizPass(NULL, $webform_submission, $element) ? 'PASSED' : 'FAILED';
    $feedback = $this->getWebformQuizFeedback(NULL, $webform_submission);

    $text = $this->t('Quiz result: @is_pass. You have answered @correct_answers out of @question_count correctly for @quiz_title. @feedback.', [
      '@quiz_title' => $quiz_title,
      '@correct_answers' => $correct_answers,
      '@question_count' => $question_count,
      '@is_pass' => $is_pass,
      '@feedback' => $feedback,
    ]);

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

    $form['quiz_score']['feedback_message_pass'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Success message'),
      '#help' => $this->t('This message will be displayed in quiz score container in case user has passed the quiz.'),
      '#required' => TRUE,
    ];

    $form['quiz_score']['feedback_message_fail'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Failed message'),
      '#help' => $this->t('This message will be displayed in quiz score container in case user has failed the quiz.'),
      '#required' => TRUE,
    ];

    $form['quiz_score']['display_on'] = [
      '#type' => 'select',
      '#title' => $this->t('Display on'),
      '#options' => $this->getDisplayOnOptions(),
    ];

    return $form;
  }

  /**
   * Get element variables for rendering.
   */
  private function getElementVariables($element, $webform_submission) {
    $score = $this->getWebformQuizScore(NULL, $webform_submission);
    $is_pass = $score >= (array_key_exists('#passing_score_percentage', $element)
      ? $element["#passing_score_percentage"] : 100);
    $message = $is_pass ? $element["#feedback_message_pass"] : $element["#feedback_message_fail"];

    return [
      '#quiz_title' => $this->getWebformQuizTitle(NULL, $webform_submission),
      '#quiz_total_questions_count' => $this->getWebformQuizElementsCount(NULL, $webform_submission),
      '#quiz_correct_answers_count' => $this->getWebformQuizCorrectAnswersCount(NULL, $webform_submission),
      '#quiz_score' => $score,
      '#quiz_is_pass' => $is_pass,
      '#quiz_feedback_message' => $message,

    ];
  }

}
