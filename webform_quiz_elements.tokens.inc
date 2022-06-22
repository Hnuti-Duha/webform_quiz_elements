<?php

/**
 * @file
 * Builds placeholder replacement tokens for webform_quiz_elements elements.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\webform_quiz_elements\Plugin\WebformQuizElementsInterface;

/**
 * Implements hook_token_info().
 */
function webform_quiz_elements_token_info() {
  $types = [];
  $tokens = [];
  $types['webform_quiz_elements'] = [
    'name' => t('Webform quiz elements'),
    'description' => t('Tokens related to webforms quiz elements.'),
    'needs-data' => 'webform',
  ];

  $quiz = [];
  $quiz['quiz_elements_count'] = [
    'name' => t('Quiz elements count'),
    'description' => t('Count of quiz elements'),
  ];

  $tokens['webform'] = $quiz;
  return ['types' => [], 'tokens' => $tokens];
}

/**
 * Implements hook_tokens().
 */
function webform_quiz_elements_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];
  if ($type === 'webform' && !empty($data['webform'])) {
    /** @var \Drupal\webform\WebformInterface $webform */
    $webform = $data['webform'];
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'quiz_elements_count':
          $replacements[$original] = _webform_quiz_elements_count($webform);
          break;
      }
    }
  }

  return $replacements;
}

/**
 * Returns count of quiz elements.
 */
function _webform_quiz_elements_count($webform) {
  $element_manager = \Drupal::service('plugin.manager.webform.element');
  $flattened_elements = $webform->getElementsInitializedFlattenedAndHasValue();
  $options = [];
  foreach ($flattened_elements as $element_key => $element) {
    $element_plugin = $element_manager->getElementInstance($element, $webform);
    if (in_array($element_plugin->getPluginId(), WebformQuizElementsInterface::QUIZ_ELEMENTS)) {
      $options[$element_key] = $element['#admin_title'];
    }
  }
  return count($options);
}
