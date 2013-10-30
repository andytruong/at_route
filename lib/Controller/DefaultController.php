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
}
