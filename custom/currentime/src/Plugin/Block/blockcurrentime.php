<?php
/**
 * @file
 * Contains \Drupal\customblock\Plugin\Block\blockcurrentime.
 */

namespace Drupal\currentime\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a 'Custom' block.
 *
 * @Block(
 *   id = "dependency_injection_services",
 *   admin_label = @Translation("Services and dependency injectionk"),
 *   category = @Translation("Custom Custom block example")
 * )
 */
class blockcurrentime extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data = \Drupal::service('currentime.CurretTime')->getData();
    return [
      '#theme' => 'my_template',
      '#data' => $data,
     
  
    ];
   }
}