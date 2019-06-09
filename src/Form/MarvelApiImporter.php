<?php

namespace Drupal\marvel_api\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\marvel_api\MarvelApi;
use Drupal\marvel_api\MarvelApiMediator;
use GuzzleHttp\Client;

class MarvelApiImporter extends FormBase {

  /**
   * Build the form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['entity_type'] = array(
      '#type' => 'select',
      '#title' => t('Selected'),
      '#options' => array(
        'characters' => t('Characters'),
        'comics' => t('Comics'),
        'creators' => t('Creators'),
      ),
      '#description' => t('Set this to <em>Yes</em> if you would like this category to be selected by default.'),
    );

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  /**
   * Get form ID.
   *
   * @return string
   */
  public function getFormId() {
    return 'marvel_api_character_import';
  }


  /**
   * Validate Form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Form submit handler.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    MarvelApiMediator::import($values['entity_type']);
  }
}
