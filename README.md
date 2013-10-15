This is just an idea, will implement when I have spare time

````
GET|POST|DELELETE /at-lazy/module_x/contact-us.[json]

  /**
   * @cache($bin = 'cache_at_route', ttl = '+ 30 minutes')
   * @permission('access content')
   * @role('role_x AND role_y OR !role_z')
   * @termimate('self::postTerminate', '\Other\Controller::doTerminate')
   */
  \Drupal\module_x\Controller\ContactUs::get|post|delete()
````

- Return JSON format if user append .json
- Support annotations: @cache, @permission, @roles, @terminate
- Think more about arguments converting
