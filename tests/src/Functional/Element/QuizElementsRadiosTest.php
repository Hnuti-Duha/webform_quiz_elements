<?php

namespace Drupal\Tests\webform_quiz_elements\Functional\Element;

use Drupal\Core\Config\FileStorage;
use Drupal\Tests\webform\Functional\Element\WebformElementBrowserTestBase;
use Drupal\webform\WebformInterface;

/**
 * Tests for the Webform Quiz Elements module.
 *
 * @group webform_quiz_elements
 */
class QuizElementsRadiosTest extends WebformElementBrowserTestBase {

  const TEST_DIR = __DIR__ . '/../../../fixtures/config';

  const QUIZ_ID = 'sample_quiz';

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = [
    'webform',
    'webform_ui',
    'webform_quiz_elements',
  ];

  /**
   * Test webform.
   *
   * @var \Drupal\webform\WebformInterface
   */
  private $webform;

  /**
   * An admin user.
   *
   * @var \Drupal\user\Entity\User
   */
  private $user;

  /**
   * Perform initial setup tasks that run before every test method.
   */
  public function setUp(): void {
    parent::setUp();

    $this->user = $this->drupalCreateUser([
      'administer webform',
    ]);
    $this->drupalLogin($this->user);

    // Load the sample quiz.
    $this->webform = $this->loadWebform(self::QUIZ_ID);
  }

  /**
   * Load a test webform.
   *
   * @param string $ymlName
   *   Name of the yml file.
   *
   * @return \Drupal\webform\WebformInterface|null
   *   A webform.
   *
   * @throws \Exception
   */
  protected function loadWebform($ymlName): WebformInterface {
    if (!file_exists(self::TEST_DIR . '/' . $ymlName . '.yml')) {
      throw new \Exception("YAML $ymlName does not exist in " . self::TEST_DIR);
    }

    $storage = \Drupal::entityTypeManager()->getStorage('webform');

    $file_storage = new FileStorage(self::TEST_DIR);
    $values = $file_storage->read($ymlName);

    /** @var \Drupal\webform\WebformInterface $webform */
    $webform = $storage->create($values);
    $webform->save();

    return $webform;
  }

  /**
   * Tests loading the sample form and accessing it.
   */
  public function testWebformLoad(): void {
    $this->assertSame(self::QUIZ_ID, $this->webform->id());
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/structure/webform/manage/' . self::QUIZ_ID);
    $assert_session->statusCodeEquals(200);
    $assert_session->pageTextContains('Minjee Lee');
  }

  /**
   * Tests saving the form with a valid submission.
   */
  public function testValidQuizSubmission(): void {
    $session = $this->getSession();
    $page = $session->getPage();
    $assert_session = $this->assertSession();

    $quiz_options = <<<EOT
brisbane:
  is_correct: false
  feedback: 'Incorrect. Try a bit further West.'
EOT;

    $this->drupalGet('admin/structure/webform/manage/' . self::QUIZ_ID . '/element/q1/edit');
    $page->fillField('edit-properties-quiz-options', $quiz_options);
    $page->pressButton('Remove item 4');
    $page->pressButton('Remove item 3');
    $page->pressButton('Remove item 2');
    $page->pressButton('Save');

    $assert_session->pageTextContains('Aussie Minjee Lee won the biggest prize ever in women\'s golf this week. Where is she from? has been updated');
  }

  /**
   * Tests saving the form with an invalid number of quiz options.
   */
  public function testInvalidQuizOptionNumber(): void {
    $session = $this->getSession();
    $page = $session->getPage();
    $assert_session = $this->assertSession();

    $quiz_options = <<<EOT
brisbane:
  is_correct: false
  feedback: 'Incorrect. Try a bit further West.'
EOT;

    $this->drupalGet('admin/structure/webform/manage/' . self::QUIZ_ID . '/element/q1/edit');
    $page->fillField('edit-properties-quiz-options', $quiz_options);
    $page->pressButton('Save');

    $assert_session->elementTextContains('css', 'div[role="alert"]', 'The number of Quiz Options (1) does not match the number of Options (4)');
  }

  /**
   * Tests saving the form with an invalid keys for quiz options.
   */
  public function testInvalidQuizOptionKey(): void {
    $session = $this->getSession();
    $page = $session->getPage();

    $quiz_options = <<<EOT
brisvegas:
  is_correct: false
  feedback: 'Incorrect. Try a bit further West.'
EOT;

    $this->drupalGet('admin/structure/webform/manage/' . self::QUIZ_ID . '/element/q1/edit');
    $page->fillField('edit-properties-quiz-options', $quiz_options);
    $page->pressButton('Remove item 4');
    $page->pressButton('Remove item 3');
    $page->pressButton('Remove item 2');
    $page->pressButton('Save');

    $this->assertSession()->elementTextContains('css', 'div[role="alert"]', 'Quiz options keys (brisvegas) do not match the Element options Option values (brisbane)');
  }

  /**
   * Tests saving the form with an invalid structure for quiz options.
   */
  public function testInvalidQuizOptionStructure(): void {
    $session = $this->getSession();
    $page = $session->getPage();

    $quiz_options = <<<EOT
brisbane:
  feedback: 'Incorrect. Try a bit further West.'
EOT;

    $this->drupalGet('admin/structure/webform/manage/' . self::QUIZ_ID . '/element/q1/edit');
    $page->fillField('edit-properties-quiz-options', $quiz_options);
    $page->pressButton('Remove item 4');
    $page->pressButton('Remove item 3');
    $page->pressButton('Remove item 2');
    $page->pressButton('Save');

    $this->assertSession()->elementTextContains('css', 'div[role="alert"]', 'Quiz option brisbane requires the is_correct value');
  }

}
