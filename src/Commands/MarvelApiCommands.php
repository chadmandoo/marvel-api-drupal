<?php

namespace Drupal\marvel_api\Commands;

use Drupal\marvel_api\MarvelApiMediator;
use Drush\Commands\DrushCommands;

/**
 * Class MarvelApiCommands
 *
 * @package Drupal\marvel_api\Commands
 */
class MarvelApiCommands extends DrushCommands {

  /**
   * Command description here.
   *
   * @param $type
   *   Argument description.
   * @param array $options
   *   An associative array of options whose values come from cli, aliases, config, etc.
   * @option offset
   *   Description
   * @usage marvel_api-importmarvelentities type
   *   Usage description
   *
   * @command marvel_api:importmarvelentities
   * @aliases ime
   */
  public function importmarvelentities($type, $options = ['offset' => 0]) {

    $available_entities = [
      'comics',
      'characters',
      'creators'
    ];

    if (in_array($type, $available_entities)) {
      MarvelApiMediator::import($type, $options['offset'], $options['verbose']);
    }
    else {
      echo "Missing type available. Please use comics, characters, and creators";
    }
  }
}
