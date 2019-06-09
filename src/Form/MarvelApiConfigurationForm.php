<?php

namespace Drupal\marvel_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Marvel API settings for this site.
 */
class MarvelApiConfigurationForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'marvel_api_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'marvel_api.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('marvel_api.settings');

    $form['marvel_api_url'] = [
      '#type' => 'textfield',
      '#title' => 'Marvel API URL',
      '#default_value' => $config->get('marvel_api_url'),
    ];
    $form['marvel_api_public'] = [
      '#type' => 'textfield',
      '#title' => 'Marvel API Public',
      '#default_value' => $config->get('marvel_api_public'),
    ];
    $form['marvel_api_private'] = [
      '#type' => 'textfield',
      '#title' => 'Marvel API Private',
      '#default_value' => $config->get('marvel_api_private'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $this->config('marvel_api.settings')
      ->set('marvel_api_url', $values['marvel_api_url'])
      ->set('marvel_api_public', $values['marvel_api_public'])
      ->set('marvel_api_private', $values['marvel_api_private'])
      ->save();

    parent::submitForm($form, $form_state);
  }
}