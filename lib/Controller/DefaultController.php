<?php
namespace Drupal\at_route\Controller;

class DefaultController {
  public static function controllerAction($class_name, $action, $arguments = array()) {
    $ctrl = new $class_name;
    return call_user_func_array(array($ctrl, $action), $arguments);
  }

  public static function fileTemplateAction($template_file, $variables) {
    if (!function_exists('at_theming_render_template')) {
      throw new \Exception('Missing at_theming module');
    }

    return at_theming_render_template($template_file, $variables);
  }

  /**
   * @todo Find a better way to know active request path, instead of $_GET['q']
   */
  public static function fileTemplateStringAction($pattern, $template_string, $variables) {
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
