<?php

// @codingStandardsIgnoreStart

/**
 * Base tasks for setting up a module to test within a full Drupal environment.
 *
 * @class RoboFile
 * @codeCoverageIgnore
 */
class RoboFile extends \Robo\Tasks {

  /**
   * Command to build the environment
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function jobBuild() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->copyConfigurationFiles());
    $collection->addTaskList($this->setUpFilesDirectory());
    $collection->addTaskList($this->runComposer());
    return $collection->run();
  }

  /**
   * Copies configuration files.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function copyConfigurationFiles() {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.github/config/settings.local.php',
        'web/sites/default/settings.local.php', $force)
      ->copy('.github/config/.env',
        '.env', $force);
    return $tasks;
  }

  /**
   * Sets up the files directory.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function setUpFilesDirectory() {
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->mkdir('web/sites/default/files')
      ->chmod('web/sites/default/files', 0777);
    return $tasks;
  }

  /**
   * Runs composer commands.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runComposer() {
    $tasks = [];
    $tasks[] = $this->taskComposerValidate()->noCheckPublish();
    $tasks[] = $this->taskComposerInstall()
      ->noInteraction()
      ->envVars(['COMPOSER_ALLOW_SUPERUSER' => 1, 'COMPOSER_DISCARD_CHANGES' => 1] + getenv())
      ->optimizeAutoloader();
    return $tasks;
  }

}