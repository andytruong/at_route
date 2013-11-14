<?php
namespace Drupal\at_route\Controller;

class DefaultController {
  public static function controllerAction($class_name, $action, $arguments = array()) {
    $ctrl = new $class_name;
    return call_user_func_array(array($ctrl, $action), $arguments);
  }

  public static function fileTemplateAction($template_file, $variables, $attached = array()) {
    $data = array(
      'template' => $template_file,
      'variables' => $variables,
      'attached' => $attached,
    );

    return at_id(new \Drupal\at_base\Helper\RenderContent($data))->render();
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

    $data = array(
      'template_string' => $template_string,
      'variables' => $variables,
      'attached' => $attached,
    );

    return at_id(new \Drupal\at_base\Helper\RenderContent($data))->render();
  }
}
