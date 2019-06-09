<?php

namespace Drupal\marvel_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiController.
 */
class ApiController extends ControllerBase {

  /**
   * Comics.
   */
  public function comics() {
    $data = [];

    $ids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'comics')
      ->range(0, 100)
      ->execute();

    if (!empty($ids)) {
      $nodes = Node::loadMultiple($ids);

      foreach ($nodes as $node) {
        $data[] = $node->toArray();
      }
    }

    return new JsonResponse($data);
  }

}
