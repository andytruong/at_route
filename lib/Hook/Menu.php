<?php
namespace Drupal\at_route\Hook;

use Drupal\at_route\Route\Importer;

class Menu {
  private $items;

  /**
   * @var Importer
   */
  private $importer;

  /**
   * [__construct description]
   */
  public function __construct(Importer $importer) {
    $this->importer = $importer;
  }

  public function getMenuItems() {
    return $this->importer->getMenuItems();
  }
}
