<?php

namespace Drupal\marvel_api;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class MarvelApi
 *
 * @package Drupal\cpmanagement
 */
class MarvelEntityFactory {

  /**
   * Create an Marvel Node.
   *
   * @param $entities
   * @param $type
   * $param $output
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function create($entities, $type, $output) {

    foreach ($entities['data']['results'] as $entity) {

      $title = self::titleCreate($entity);

      if ($title) {
        $entities = \Drupal::service('eqd')
          ->create()
          ->getStorage('node')
          ->findBy([
            'field_id' => $entity['id'],
            'type' => $type,
          ])
          ->load();

        if (empty($entities)) {
          $ent = Node::create(
            [
              'title' => $title,
              'type' => $type,
              'field_id' => $entity['id'],
            ]
          );

          $created = TRUE;
        }
        else {
          $ent = current($entities);
        }

        $ent->set('field_modified', self::apiDate($entity['modified']));
        $ent->set('field_resource_uri', ['uri' => $entity['resourceURI']]);
        $ent->set('field_thumbnail', $entity['thumbnail']['path']);
        $ent->set('field_thumbnail_extension', $entity['thumbnail']['extension']);

        if (isset($entity['description'])) {
          $ent->set('field_description', $entity['description']);
        }

        self::additionalData($entity, $ent, $type);

        $ent->save();

        if ($output) {

          $message = isset($created) ? 'Created' : 'Updated';

          echo $message . " " . $type . ' ' . $ent->getTitle() . "\n";
        }
      }
    }
  }

  /**
   * Transform Title
   *
   * @param $entity
   *
   * @return mixed|string
   */
  public static function titleCreate($entity) {
    $title = '';
    if (isset($entity['name'])) {
      $title = $entity['name'];
    }
    else {
      if (isset($entity['title'])) {
        $title = $entity['title'];
      }
    }

    if (!isset($title) || !$title) {
      if (isset($entity['fullName']) && $entity['fullName']) {
        $title = $entity['fullName'];
      }

      if (!$title) {
        if (isset($entity['firstName']) && $entity['firstName']) {
          $title .= $entity['firstName'];
        }

        if (isset($entity['middleName']) && $entity['middleName']) {
          $title .= $entity['middleName'];
        }

        if (isset($entity['lastName']) && $entity['lastName']) {
          $title .= $entity['lastName'];
        }

        if (isset($entity['suffix']) && $entity['suffix']) {
          $title .= $entity['suffix'];
        }
      }
    }

    return $title;
  }

  /**
   * Transform Date
   *
   * @param $date
   *
   * @return false|string
   */
  public static function apiDate($date) {
    $field_date = date('Y-m-d', strtotime($date));
    $field_date .= 'T';
    $field_date .= date('H:i:s', strtotime($date));

    return $field_date;
  }

  /**
   * Additional Data.
   *
   * @param $entity
   * @param $ent
   * @param $type
   */
  public static function additionalData($entity, &$ent, $type) {
    switch ($type) {
      case 'comics':
        self::comics($entity, $ent);
        break;
    }
  }

  /**
   * Additional data for comics.
   *
   * @param $entity
   * @param $ent
   */
  public static function comics($entity, &$ent) {
    $ent->set('field_digital_id', $entity['digitalId']);
    $ent->set('field_issue_number', $entity['issueNumber']);
    $ent->set('field_isbn', $entity['isbn']);
    $ent->set('field_upc', $entity['upc']);
    $ent->set('field_diamond_code', $entity['diamondCode']);
    $ent->set('field_ean', $entity['ean']);
    $ent->set('field_issn', $entity['issn']);

    if ($entity['format']) {
      $ent->set('field_format', self::createTaxonomy('format', $entity['format']));
    }

    $ent->set('field_page_count', $entity['pageCount']);

    $ent->set('field_characters', self::findByResource($entity['characters']));
    $ent->set('field_creators', self::findByResource($entity['creators']));
  }

  /**
   * Find or create taxonomy.
   *
   * @param $vocab_name
   * @param string $term
   *
   * @return int|string|null
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function createTaxonomy($vocab_name, $term = '') {
    $terms = \Drupal::service('eqd')
      ->create()
      ->getStorage('taxonomy_term')
      ->findBy([
        'name' => $term,
        'vid' => $vocab_name,
      ])
      ->load();

    if (!empty($terms)) {
      $term = current($terms);
    }
    else {
      $term = Term::create([
        'name' => $term,
        'vid' => $vocab_name,
      ]);

      $term->save();
    }

    return $term->Id();
  }

  /**
   * Find by Resource URI
   *
   * @param $resources
   */
  public static function findByResource($resources) {
    $items = [];

    foreach ($resources['items'] as $resource) {

      $ids = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('field_resource_uri', $resource['resourceURI'])
        ->execute();

      if (!empty($ids)) {
        $items[] = current($ids);
      }
    }

    return $items;
  }
}