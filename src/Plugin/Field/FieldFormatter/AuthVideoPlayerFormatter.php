<?php

/**
 * @file
 * Contains Drupal\video_permission\Plugin\Field\FieldFormatter\AuthVideoPlayerFormatter.
 */

namespace Drupal\video_permission\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\video\Plugin\Field\FieldFormatter\VideoPlayerFormatter;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;

/**
 * Plugin implementation of the 'auth_video_player' formatter.
 *
 * @FieldFormatter(
 *   id = "auth_video_player",
 *   label = @Translation("Video Player For Authenticated User Only"),
 *   field_types = {
 *     "video"
 *   }
 * )
 */

class AuthVideoPlayerFormatter extends VideoPlayerFormatter {
    /**
     * {@inheritdoc}
     */
    public static function defaultSettings() {
      return array(
        'selected_role' => 'norestrict'
      ) + parent::defaultSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state) {
      $element = parent::settingsForm($form, $form_state);
      $roles =  ["norestrict" => "All User"] + user_role_names(FALSE, NULL);
      $element['selected_role'] = [
          '#title' => t('Only For Selected Role'),
          '#type' => 'select',
          '#default_value' => $this->getSetting('selected_role'),
          '#options' => $roles
      ];
      return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary() {
      $summary = array();
      $summary[] = t('Video Player (@selected_role@widthx@height@controls@autoplay@loop@muted).', [
        '@selected_role' => $this->getSetting('selected_role'),
        '@height' => $this->getSetting('height'),
        '@height' => $this->getSetting('height'),
        '@controls' => $this->getSetting('controls') ? t(', controls') : '' ,
        '@autoplay' => $this->getSetting('autoplay') ? t(', autoplaying') : '' ,
        '@loop' => $this->getSetting('loop') ? t(', looping') : '' ,
        '@muted' => $this->getSetting('muted') ? t(', muted') : '',
      ]);
      return $summary;
    }


    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
      $elements = array();
      $files = $this->getEntitiesToView($items, $langcode);

      // Early opt-out if the field is empty.
      if (empty($files)) {
        return $elements;
      }

      // Collect cache tags to be added for each item in the field.
      foreach ($files as $delta => $file) {
        $video_uri = $file->getFileUri();
        $theme = 'video_player';
        if ($this->currentUser) {
            if ( !in_array($this->getSetting('selected_role'), $this->currentUser->getRoles()) &&
                  $this->getSetting('selected_role') != "norestrict") {
                      $theme = 'access_denied';
            }
        }

        $elements[$delta] = array(
          '#theme' => $theme,
          '#items' => array(Url::fromUri(file_create_url($video_uri))),
          '#player_attributes' => $this->getSettings(),
        );
      }
      return $elements;
    }
}
