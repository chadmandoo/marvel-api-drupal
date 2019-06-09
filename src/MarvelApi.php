<?php

namespace Drupal\marvel_api;

use GuzzleHttp\Client;

/**
 * Class MarvelApi
 *
 * @package Drupal\cpmanagement
 */
class MarvelApi {

  private $client;

  /**
   * MarvelApi constructor.
   *
   * @param \GuzzleHttp\Client $client
   */
  public function __construct(Client $client) {
    $this->client   = $client;
    $this->url      = \Drupal::config('marvel_api.settings')->get('marvel_api_url');
    $this->public   = \Drupal::config('marvel_api.settings')->get('marvel_api_public');
    $this->private  = \Drupal::config('marvel_api.settings')->get('marvel_api_private');
  }

  /**
   * Perform the API Call.
   *
   * @param $url
   * @param int $offset
   * @param int $limit
   *
   * @return array|mixed
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function call($url, $offset = 0, $limit = 20) {
    $data = [];

    try {
      $time = time();
      $hash = md5($time . $this->private . $this->public);
      $params = '?ts=' . $time . '&hash=' . $hash . '&apikey=' . $this->public . '&offset=' . $offset . '&limit=' . $limit;

      $res = $this->client->request('GET', $this->url . '/' . $url . $params, []);
      $body = $res->getBody();
      $data = json_decode($body->getContents(), TRUE);

    } catch(\Exception $e) {
      $messenger = \Drupal::messenger();
      if (isset($message)) {
        $messenger->addMessage($e->getMessage(), 'error');
      }
    }

    return $data;
  }
}