<?php
namespace Drupal\at_route\Route;

class Importer {
  public function getMenuItems() {
    $items = array();
    foreach (at_modules('at_route') as $module) {
      $path = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/config/route.yml';
      if (file_exists($path)) {
        $items += $this->importResource($module, $path);
      }
    }
    return $items;
  }

  private function importResource($module, $path) {
    $items = array();
    if ($data = at_config_read_yml($path)) {
      if (!empty($data['routes'])) {
        foreach ($data['routes'] as $route_name => $route_data) {
          if ($item = $this->importRoute($module, $route_name, $route_data)) {
            $items[$route_name] = $item;
          }
        }
      }
    }
    return $items;
  }

  private function importRoute($module, $route_name, $route_data) {
    if ($item = $this->convertRouteDataToMenuItem($module, $route_name, $route_data)) {
      return $this->prepareMagicProperties($item);
    }
  }

  private function getRouteKeyMapping() {
    $parse_constant = function($v) {
      if (preg_match('/^[A-Z_]+$/', $v)) return constant($v);
      $part = preg_split('/\s+[\|&]\s+/', $v);
      if (count($part)) {
        foreach ($part as $i => $constant) $part[$i] = constant($constant);
        return strpos($v, '|') ? ($part[0] | $part[1]) : $part[0] & $part[1];
      }
    };

    $mapping = array(
      'no_convert'   => array(
        'title', 'title arguments', 'description', 'page callback', 'page arguments', 'access callback', 'access arguments', 'theme callback', 'theme arguments', 'file', 'file path', 'load arguments', 'weight', 'menu_menu', 'expanded', 'tab_parent', 'tab_root', 'position',
        'template', 'controller', 'variables'
      ),
      'convert'      => array(
        'context'    => $parse_constant,
        'type'       => $parse_constant,
      ),
    );

    return $mapping;
  }

  /**
   * We can not define constants in yaml, this method is to convert them.
   */
  private function convertRouteDataToMenuItem($module, $route_name, $route_data) {
    $mapping = $this->getRouteKeyMapping();

    foreach ($mapping['no_convert'] as $k) {
      if (isset($route_data[$k])) {
        $item[$k] = $route_data[$k];
      }
    }

    foreach ($mapping['convert'] as $k => $callback) {
      if (isset($route_data[$k])) {
        $item[$k] = call_user_func_array($callback, array($route_data[$k]));
      }
    }

    return !empty($item) ? $item + array('file path' => drupal_get_path('module', $module)) : array();
  }

  private function prepareMagicProperties($item) {
    if (!empty($item['controller'])) {
      $item['page callback'] = '\Drupal\at_route\Controller\DefaultController::controllerAction';
      $item['page arguments'] = $item['controller'];
    }

    if (!empty($item['template'])) {
      $item['page callback'] = '\Drupal\at_route\Controller\DefaultController::fileTemplateAction';
      $item['page arguments'] = array('template'  => $item['template'], 'variables' => !empty($item['variables']) ? $item['variables'] : array());
    }

    return $item;
  }
}
