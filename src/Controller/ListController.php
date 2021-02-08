<?php

/**
 * @file
 * Contains \Drupal\nodekey\Controller\ListController.
 */

namespace Drupal\nodekey\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\nodekey\Entity\NodeKeyEntity;

/**
 * List all node keys
 */
class ListController extends ControllerBase {

  const ITEMS_PER_PAGE = 20;

  /**
   * Displays all node keys.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function index() {

	$language = \Drupal::languageManager()->getCurrentLanguage()->getId();


	$pager_manager = \Drupal::service('pager.manager');
	$pager_parameters = \Drupal::service('pager.parameters');
	$page = max(0, $pager_parameters->findPage());

    $offset = $page * self::ITEMS_PER_PAGE;

    $query = \Drupal::database()->select('node', 'n');
    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->join('node__nodekey', 'nk', 'nk.entity_id = n.nid');
    $query->addField('n', 'nid', 'nid');
    $query->addField('nfd', 'title', 'title');
    $query->addField('nk', 'nodekey_value', 'key');
    $query->condition('nfd.langcode', $language);
    $query->orderby('n', 'nid', 'ASC');

    $query_count = clone $query;

    $query->range($offset, self::ITEMS_PER_PAGE);
    $stmt = $query->execute();
    $results = $stmt->fetchAll();

    $query_count = $query_count->countQuery();
    $stmt_count = $query_count->execute();
    $count = $stmt_count->fetchField();

	$pager_manager->createPager($count, self::ITEMS_PER_PAGE);

    $tid = 'nodekeys_table';
    $render = array(
      $tid => array(
        '#type' => 'table',
        '#header' => array(
          '#',
          t('Title'),
          t('Node key')
        ),
        '#rows' => array(),
        '#empty' => t('There are no items yet.')
      ),
      'pager' => array(
        '#type' => 'pager'
      )
    );

    foreach($results as $row) {
      $render[$tid]['#rows'][$row->nid] = array(
        'data' => array(
          'nid' => array(
            'data' => $row->nid
          ),
          'title' => array(
            'data' => $row->title
          ),
          'key' => array(
            'data' => $row->key
          )
        )
      );
      if (NodeKeyEntity::count($row->key) > 1) {
        $render[$tid]['#rows'][$row->nid]['class'] = 'color-error';
      }
    }

    return $render;
  }

}
