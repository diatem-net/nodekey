<?php

/**
 * @file
 * Contains \Drupal\nodekey\Form\ConfigForm.
 */

namespace Drupal\nodekey\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nodekey_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $config = $this->config('nodekey.settings');

    $content_types = NodeType::loadMultiple();
    $node_type_titles = array();
    foreach ($content_types as $machine_name => $val) {
      $node_type_titles[$machine_name] = $val->label();
    }
    $form['content_types'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Content types'),
      '#description' => $this->t('All these content types will use the unique key system.'),
      '#options' => $node_type_titles,
      '#default_value' => $config->get('content_types'),
    );

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('nodekey.settings');
    $config->set('content_types', $form_state->getValue('content_types'));
    $config->save();

    foreach ($config->get('content_types') as $bundle => $value) {
      $field = FieldConfig::loadByName('node', $bundle, 'nodekey');

      if ($value === 0 && !is_null($field)) {
        // Remove field instance from content types which no longer uses it
        $field->delete();
      }

      if ($value !== 0 && is_null($field)) {
        // Create field instance for new content types
        $field = FieldConfig::create(array(
          'field_name' => 'nodekey',
          'entity_type' => 'node',
          'bundle' => $bundle,
          'label' => $this->t('Node key'),
          'description' => $this->t('This key is used to identify content without relying on its identifier.<br><b>The content key must be unique.</b>'),
          'required' => true,
          'translatable' => false
        ));
        $field->save();
      }
    }

    return parent::submitForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {

    return [
      'nodekey.settings',
    ];

  }

}