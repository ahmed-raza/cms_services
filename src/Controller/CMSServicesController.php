<?php

namespace Drupal\cms_services\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CMSServicesController.
 */
class CMSServicesController extends ControllerBase {

  /**
   * Content.
   *
   * @return JsonResponse
   *   Return nodes.
   */
  public function content($type = null) {
    $nids = \Drupal::entityQuery('node')->condition('type', $type)->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    $bundle_fields = \Drupal::entityManager()->getFieldDefinitions('node', $type);

    $data = [];
    foreach ($nodes as $nid => $node) {
      foreach ($bundle_fields as $field) {
        if ($node->hasField($field->getName())) {
          $data[$nid][$field->getName()] = $node->{$field->getName()}->getValue();
          if ($field->getType() == 'entity_reference' && $field->getSetting('target_type') == 'taxonomy_term') {
            foreach ($data[$nid][$field->getName()]['target_id'] as $tid) {
              kint($tid);
              // $data[$nid][$field->getName()][] = ['name'=>taxonomy_term_load($tid['target_id'])->getName()];
            }
          }
        }
      }
    }
    die;

    $response = [
      'type' => $type,
      'nodes' => $data,
    ];
    return new JsonResponse($response);
  }

}
