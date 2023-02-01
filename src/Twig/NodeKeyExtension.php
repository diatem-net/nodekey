<?php

namespace Drupal\nodekey\Twig;

use Drupal\nodekey\Entity\NodeKeyEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Add a nkurl() function to Twig
 */
class NodeKeyExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'nodekey.twig_extension';
  }

  /**
   * Generates a list of all Twig functions that this extension defines.
   */
  public function getFunctions() {
    return array(
      new TwigFunction(
        'nk',
        array($this, 'nk'),
        array('is_safe' => array('html'))
      ),
      new TwigFunction(
        'nkurl',
        array($this, 'nkurl'),
        array('is_safe' => array('html'))
      )
    );
  }

  /**
   * Load a node based on its node key
   */
  public static function nk($key) {
    return NodeKeyEntity::load($key);
  }

  /**
   * Display an URL based on a node key
   */
  public static function nkurl($key) {
    return NodeKeyEntity::url($key);
  }

}
