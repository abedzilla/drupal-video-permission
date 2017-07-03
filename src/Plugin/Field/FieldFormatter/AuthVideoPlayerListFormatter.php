<?php

/**
 * @file
 * Contains \Drupal\video\Plugin\Field\FieldFormatter\AuthVideoPlayerListFormatter.
 */

namespace Drupal\video_authenticated_user\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
// use Drupal\video\Plugin\Field\FieldFormatter\VideoPlayerListFormatter;

/**
 * Plugin implementation of the 'video_player_list' formatter.
 *
 * @FieldFormatter(
 *   id = "auth_video_player_list",
 *   label = @Translation("Video Player For Authenticated User Only"),
 *   field_types = {
 *     "video"
 *   }
 * )
 */

class AuthVideoPlayerListFormatter extends AuthVideoPlayerFormatter {
    /**
     * {@inheritdoc}
     */
    public static function isApplicable(FieldDefinitionInterface $field_definition) {
      if(empty($field_definition->getTargetBundle()) && $field_definition->isList()){
        return TRUE;
      }
      else{
        $entity_form_display = entity_get_form_display($field_definition->getTargetEntityTypeId(), $field_definition->getTargetBundle(), 'default');
        $widget = $entity_form_display->getRenderer($field_definition->getName());
        $widget_id = $widget->getBaseId();
        if($field_definition->isList() && $widget_id == 'video_upload'){
          return TRUE;
        }
      }
      return FALSE;
    }
}
