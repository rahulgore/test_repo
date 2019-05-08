<?php

/**
 * @file
 * Contains \Drupal\custom_article\Controller\CategorizedArticleController.
 */

namespace Drupal\custom_article\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Block\Plugin\Block;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Path\AliasManager;
use Drupal\Core\GeneratedUrl;
use Drupal\Core\Language\Language;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Utility\LinkGenerator;
use Drupal\Core\GeneratedLink;
use Drupal\Core\Entity\Query;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;



class CategorizedArticleController extends ControllerBase {

  public function cat_article_main($category) {


    $term = Term::load($category);
    $name = $term->getName();
    $tlink = $term->url();

    $host = \Drupal::request()->getHost();
    global $base_url;

    $catlink = 'http://'.$host.$tlink;

    /*
    //get the alias of term
    $aliasManager = \Drupal::service('path.alias_manager');
    // The second argument to getAliasByPath is a language code such as "en" or LanguageInterface::DEFAULT_LANGUAGE.
    $more_url = $aliasManager->getAliasByPath('/taxonomy/term/' . $category);
    /**/

    //creating the Link
    //$url = Url::fromRoute('<front>', [], ['attributes' => ['class' => ['foo', 'bar']]]);
    $url = Url::fromUri($catlink);
    $url->setUrlGenerator($this->urlGenerator);
    $link = Link::fromTextAndUrl('More '.$name, $url)->toString();
    /**/

    /*
    //another method to create link
    $url2 = Url::fromUri($catlink);
    $external_link = \Drupal::l(t('More '.$name), $url2);
    /**/

    $nodes = CategorizedArticleController::get_cat_articles($category);
    $node_list_html = '';
    //print '<pre>';print_r($nodes);print '</pre>';
    foreach($nodes as $node) {
      $node_list_data = CategorizedArticleController::cat_article_listing($node);
      $node_listing = \Drupal::service('renderer')->render($node_list_data);
      //print '<pre>';
      //print($node_listing);
      //print '</pre>';
      $node_list_html .= $node_listing;
    }
      //$node_list_data = \Drupal::service('renderer')->render($node_listing);
    return [
      '#theme' => 'categorized_article_main',
      '#category_name' => $name,
      '#category_linked' => $link,
      //'#categorized_article_listing' => 'Listing here...',
      '#categorized_article_listing' => $node_list_html,
      //'variables' => ['category_name' => 'cat2', 'category_linked' => 'More Cat2', 'categorized_article_listing' => 'Listing here2' ],
      //'#test_var' => t('Test Value'),
    ];/**/
  }

  public function get_cat_articles($category) {

    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'news_article');
    $query->condition('status', 1);
    $query->condition('field_news_articles_terms', $category);
    $query->sort('field_post_date', DESC);
    $query->range(0,10);
    $result = $query->execute();
    $nids = array_values($result);
    //print '<pre>';print_r($nids);print '</pre>';
    // Get a node storage object.
    //$node_storage = \Drupal::entityManager()->getStorage('node');
    $nodes_data = Node::loadMultiple($nids);
    //print '<pre>';print_r($nodes_data);print '</pre>';
    /*
    foeach($nodes as $node) {
      //do something
      print '<pre>';print_r($node);print '</pre>';
    }/**/
    return $nodes_data;

  }

  public function cat_article_listing($node) {

    //print '<pre>';print_r($node);print '</pre>';
    //print $node->get('nid')->value;
    //\Drupal::moduleHandler()->getModule('custom_article')->getPath();
    $image_uri = '';
    $pri_name = '';
    $nid = $node->get('nid')->value;

    $options = array('absolute'=>TRUE);
    $url = Url::fromRoute('entity.node.canonical', [ 'node'=>$nid ], $options);
    $title_url = $url->toString();
    $turl = Url::fromUri($title_url);
    //$turl->setUrlGenerator($this->urlGenerator);
    $title = $node->get('title')->value;
    $title_link = Link::fromTextAndUrl($title, $turl)->toString();

    if(!$node->get('field_article_image')->isEmpty()){
      $image_uri = ImageStyle::load('bnc_article_category_page')->buildUrl($node->field_article_image->entity->getFileUri());
    }
    if(!$node->get('field_news_articles_terms')->isEmpty()){
      $pri_cat = $node->get('field_news_articles_terms')->target_id;
      $pri_term = Term::load($pri_cat);
      $pri_name = $pri_term->getName();
    }
    $def_img = file_create_url(drupal_get_path('module', 'custom_article') . '/images/bnc_cat_page_default_image.png');

    return [
      '#theme' => 'categorized_article_listing',
      '#title' => $title_link,
      '#default_img' => $def_img,
      '#image' => $image_uri,
      '#primary_category' => $pri_name,

    ];

  }
}
