<?php

/**
 * @file
 * Contains \Drupal\nodekey\Entity\NodeKeyEntity.
 */

namespace Drupal\nodekey\Entity;

use Drupal\node\Entity\Entity;
use Drupal\node\Entity\Node;

class NodeKeyEntity {

  /**
   * Loads an entity relying on its node key.
   *
   * @param mixed $key
   *   The unique key of the entity to load.
   *
   * @return static
   *   The entity object or null if there is no entity with the given key.
   */
  public static function load($key) {

    $config = \Drupal::config('nodekey.settings');
    $content_types = array_filter($config->get('content_types'), function($element) {
      return $element !== 0;
    });

    $query = \Drupal::entityQuery('node');
    $query->condition('type', array_values($content_types), 'IN');
    $query->condition('nodekey', $key);
    $nids = $query->execute();

    if (!empty($nids)) {
      $node = entity_load('node', reset($nids));
      if ($node) {
        return $node;
      }
    }

    return null;

  }

  /**
   * Return the number of entities with a given key
   *
   * @param string $key
   *   The unique key to search.
   *
   * @return integer
   *   The number of entities with a given key
   */
  public static function count($key) {

    $query = \Drupal::database()->select('node__nodekey', 'nk');
    $query->condition('nk.nodekey_value', $key);
    $stmt = $query->execute();
    $results = $stmt->fetchAll();

    return count($results);

  }

  /**
   * Create a key for a node
   *
   * @param Node $node
   *   The node for which to create a key
   *
   * @return string
   *   The created key
   */
  public static function create($node) {

    $content_types = \Drupal::config('nodekey.settings')->get('content_types');
    if ($content_types) {
        foreach ($content_types as $key => $bundle) {
            if ($bundle === 0) {
                continue;
            }
            if ($bundle == $node->getType()) {
                
                $nodekey = strtolower($node->getTitle());
                $nodekey = preg_replace('/[^a-z\d]+/', '-', $nodekey);
                $nodekey = trim($nodekey, '-');

                $i = -1;
                do {
                  $_nodekey = $nodekey;
                  if (++$i > 0) {
                    $_nodekey .= '-'.$i;
                  }
                } while ($existing = self::load($_nodekey));

                if ($i > 0) {
                  $nodekey .= '-'.$i;
                }
                return $nodekey;

            }
        }
    }
    return null;

  }

  /**
   * Gets an entity's url relying on its node key.
   *
   * @param mixed $key
   *   The unique key of the entity to load.
   *
   * @return static
   *   The entity's url or null if there is no entity with the given key.
   */
  public static function url($key) {

    if ($entity = self::load($key)) {
      return $entity->url();
    }

    return null;

  }

}