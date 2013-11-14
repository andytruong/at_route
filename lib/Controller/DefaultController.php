<?php
namespace Drupal\at_route\Controller;

class DefaultController {
  public static function controllerAction($class_name, $action, $arguments = array()) {
    $ctrl = new $class_name;
    return call_user_func_array(array($ctrl, $action), $arguments);
  }

  private static function processAssetPath($path) {
    $path = str_replace('%theme', path_to_theme(), $path);
    return $path;
  }

  private static function processAttachedAsset($attached) {
    if (empty($attached)) return $attached;

    foreach (array_keys($attached) as $type) {
      foreach ($attached[$type] as $k => $item) {
        if (is_string($item)) {
          $attached[$type][$k] = self::processAssetPath($item);
        }
      }
    }

    return $attached;
  }

  public static function fileTemplateAction($template_file, $variables, $attached = array()) {
    if (!function_exists('at_theming_render_template')) {
      throw new \Exception('Missing at_theming module');
    }

    return array(
      '#markup' => at_theming_render_template($template_file, $variables),
      '#attached' => self::processAttachedAsset($attached),
    );
  }

  /**
   * @todo Find a better way to know active request path, instead of $_GET['q']
   */
  public static function fileTemplateStringAction($pattern, $template_string, $variables, $attached = array()) {
    $item = menu_get_item($_GET['q']);
    $path = explode('/', $pattern);
    foreach ($path as $i => $part) {
      if (strpos($part, '%') === 0) {
        $part = substr($part, 1);
        $variables[$part] = $item['map'][$i];
      }
    }

    return at_theming_render_string_template($template_string, $variables);
  }
}
