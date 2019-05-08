<?php

namespace Drupal\custom_article\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;

use Drupal\Core\Render;
use Drupal\custom_article\Controller\CategorizedArticleController;




/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "custom_article_categorized_articles_block",
 *   admin_label = @Translation("Categorized Article Listing"),
 *   category = @Translation("Custom article")
 * )
 */
class CategorizedArticlesBlock extends BlockBase  implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */

  public function build() {
    /*return [
      '#markup' => $this->t('This is a simple block! '),
    ];*/
    $config = $this->getConfiguration();
    $catdata = $config['category'];
    //print_r($catdata);
    $cat_article_list_data = CategorizedArticleController::cat_article_main($catdata);
    $cat_article_listing = \Drupal::service('renderer')->render($cat_article_list_data);
    //print_r($cat_article_list_data);
    //print_r($cat_article_listing);

    /*
    return array(
      '#markup' => $this->t('This is a simple block! '),
    );/**/

    return [
      '#markup' => $cat_article_listing,
    ];/**/


  }

   /**
   * {@inheritdoc}
   */
  /*public function defaultConfiguration() {

  }*/


  /**
   * {@inheritdoc}
   */
  /*
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }*/


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $vid = 'news';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $category_options = array();
    foreach ($terms as $term) {
      $category_options[$term->tid] = $term->name;
    }

     $config = $this->getConfiguration();

    $form['category'] = array(
      '#type' => 'select',
      '#title' => t('Category'),
      '#description' => t('Choose the category.'),
      '#options' => $category_options,
      '#default_value' => $this->configuration['category'],
      //'#default_value' => isset($config['category']) ? $config['category'] : '',
    );

    $form['category2'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Hello message'),
      '#default_value' => $this->configuration['category2'],

    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('category', $form_state->getValue('category'));
    $this->setConfigurationValue('category2', $form_state->getValue('category2'));
  }

}