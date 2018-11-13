<?php

namespace Josheli;

use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;

class Instagrammer {

  protected $ig_user;
  protected $download_dir;

  public function __construct($ig_user, $download_dir)
  {
    $this->ig_user = $ig_user;
    $this->download_dir = $download_dir;
  }

  /**
   * @param $username
   * @param $max
   *
   * @return \InstagramScraper\Model\Media[]
   * @throws \InstagramScraper\Exception\InstagramException
   * @throws \InstagramScraper\Exception\InstagramNotFoundException
   */
  public function getInstagramMedia($username, $max = 30)
  {
    $instagram = new Instagram();

    $medias = [];
    $max_id = '';
    do {
      $response = $instagram->getPaginateMedias($username, $max_id);
      $max_id = $response['maxId'];

      if($response['medias'])
      {
        $medias = array_merge($medias, $response['medias']);
      }

      if(count($medias) > $max)
      {
        break;
      }

      sleep(1);

    } while($response['hasNextPage']);

    return $medias;
  }

  /**
   * @throws \InstagramScraper\Exception\InstagramException
   * @throws \InstagramScraper\Exception\InstagramNotFoundException
   */
  public function downloadInstagramMedias()
  {
    $medias = $this->getInstagramMedia($this->ig_user);

    foreach($medias as $media)
    {
      $this->downloadInstagramMedia($media);
    }
  }

  /**
   * @param \InstagramScraper\Model\Media $media
   */
  public function downloadInstagramMedia(Media $media)
  {
    $ig_url = $media->getImageHighResolutionUrl();
    if(!$ig_url)
    {
      $ig_url = $media->getVideoStandardResolutionUrl();
    }

    if($ig_url)
    {
      $file_name = basename(parse_url($ig_url, PHP_URL_PATH));
      $file_path = $this->download_dir . $file_name;
      file_put_contents($file_path, file_get_contents($ig_url));
      file_put_contents($file_path . '.json', json_encode([
        'created' => $media->getCreatedTime(),
        'caption' => $media->getCaption(),
        'link' => $media->getLink()
      ]));
    }

  }
}