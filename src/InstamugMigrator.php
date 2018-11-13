<?php
namespace Josheli;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class InstamugMigrator extends CLI
{
  protected $ig_user = '';
  protected $download_dir = '';

  /** @var null|\Josheli\Instagrammer */
  protected $ig = null;

  public function setup(Options $options)
  {
    $options->setHelp('Migrate Instagram photos to SmugMug');

    $options->registerOption(
      'ig-user',
      'Instagram user name',
      null,
      true
    );

    $options->registerOption(
      'download-dir',
      'Directory to download IG media (defaults to current directory). IG username directory will be created inside given directory.',
      null,
      true
    );
  }

  /**
   * @param \splitbrain\phpcli\Options $options
   *
   * @throws \InstagramScraper\Exception\InstagramException
   * @throws \InstagramScraper\Exception\InstagramNotFoundException
   */
  public function main(Options $options)
  {
    $this->ig = new Instagrammer(
      $this->getIgUser(),
      $this->getDownloadDir()
    );

    $this->ig->downloadInstagramMedias();

  }

  protected function getIgUser()
  {
    static $ig_user;
    if(!$ig_user)
    {
      $ig_user = $this->options->getOpt('ig-user');
      if(!$this->ig_user)
      {
        echo $this->options->help();
        exit;
      }
    }
    return $ig_user;
  }

  protected function getDownloadDir()
  {
    static $download_dir;
    if(!$download_dir)
    {
      $directory = $this->options->getOpt('download-dir', dirname(__DIR__));

      if(!is_dir($directory) || !is_writable($directory))
      {
        $this->error("Given download-dir is not writable.");
        echo $this->options->help();
        exit;
      }

      $download_dir = $directory . DIRECTORY_SEPARATOR . $this->getIgUser() . DIRECTORY_SEPARATOR;

      if(!is_dir($this->download_dir))
      {
        mkdir($this->download_dir);
      }

      if(!is_dir($this->download_dir) || !is_writable($this->download_dir))
      {
        $this->error("Could not create download dir for given ig-user.");
        echo $this->options->help();
        exit;
      }
    }

    return $download_dir;
  }
}