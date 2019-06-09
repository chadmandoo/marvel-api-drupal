<?php

namespace Drupal\marvel_api;

use GuzzleHttp\Client;

/**
 * Class MarvelApiMediator
 *
 * @package Drupal\cpmanagement
 */
class MarvelApiMediator {

  /**
   * Import mediator.
   *
   * @param $type
   * @param $offset
   * @param $output
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public static function import($type, $offset = 0, $output = FALSE) {
    do {
      $api = new MarvelApi(new Client());
      $entities = $api->call('/' . $type, $offset);


      if (isset($entities['data']['total'])) {
        $end = $entities['data']['total'];

        MarvelEntityFactory::create($entities, $type, $output);
        $offset += 20;

        if ($output) {
          echo "Total: " . $offset;
        }
      }
      else {
        sleep(10);
      }


    } while ($offset < $end);
  }
}