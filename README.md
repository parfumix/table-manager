##Introduction
Table manager provide a easy way to render you data using tables. You can easy filter your table or adding an pagination.

##Basic usage

For the first you have to provider your data in one of the supportable format. By the moment you can provide your data ass php Array, Eloquent or file.

```php

 use Flysap\TableManager;

 $data = array(
    'columns' => array('id', 'name', 'last name', 'password'),
    'rows'    => array(
        array(1, 'name', 'last name', 'password'),
        array(2, 'name', 'last name', 'password')
    )
 );

 $table = TableManager\table('Collection', $data);

 echo $table->render();
```

 as the result will be rendered below table

| id     | name       | last name  | password |
| ------ |:----------:| ----------:| --------:|
| 1      | James      | Brow       | 12345    |
| 2      | Ivan       | Berezovski | 12345    |


##Advanced usage

You can provider additional arguments for each rendered column


#### Permissions

```php

 $data = array(
    'columns' => array('name' => ['roles' => [admin, editor]]) // will be visible just for admin and editor roles
 );

 $data = array(
     'columns' => array('name' => ['permissions' => [is_admin, is_editor]]) // will be visible just for user has that permissions
  );

```

#### Formatting columns

```php

  # You can set custom labels for one of columns
  $data = array(
     'columns' => array('name' => ['label' => 'Admin name']])
  );

  # Will be set class or id for current column
  $data = array(
     'columns' => array('name' => ['class' => 'th-class', 'id' => 'th-id']]);
  );

  # You can add own template to format value
  $data = array(
      'columns' => array('name' => ['template' => '<span>%s</span>' or function($value) {  return str_replace('%s', $value, '<span>%s</span>'); }, ]])
  );

  $data = array(
      'columns' => array('name' => ['before' => '<span>', 'after' => '</span>' ]])
  );

  # You can format you value using closure function
  $data = array(
      'columns' => array('name' => ['closure' => function($value) { return $value; } ]])
  );

  # To sort data by specific column simply use sortable attribute
  $data = array(
      'columns' => array('name' => ['sortable' => true ]])
  );

```

#### Pagination

 Sometimes table can have thousand of rows and will take time and resources to render all that rows in an single page.
 
 Will be smarter if you paginate your table using pagination. By default when you create an instance of table is applied default configurations which you can see in **general.yaml** file alias called default.
  
```yaml
  templates:
   default:
    perPage: 12
 ```
  As result your table will render 12 rows per page. You can easy edit it by adding your template alias or edit default.
  
  
  ```php
   # Will be rendered pagination
   echo $table->paginate();
  ```    

   or you can use helper functions:

   ```php
   use Flysap\TableManager;

   echo TableManager\render_pagination($table);
   ```

#### Drivers

  Package provide an predefined list of drivers which in some of case is not enough to work with it. If you have data on other formats than eloquent or php array you can easy create your driver which
    do base stuff like **getData** .

    To create own driver you have create an driver class which will implement *Flysap\TableManager\DriverAble* and implement functions from there.

```php
class MyDriver implements  Flysap\TableManager\DriverAble  {

    public function filter(\Closure $filter, $params = array()) {
        # filter your data .
    }
    
    public function getData() {
        return [
            'columns' => 'columns as array',
            'rows'    => 'rows as array',
        ];
    }
}
```
    after all of that you have to register driver namespace in module configuration file.

```yaml
# here will be declared all driver namespaces .
driver_namespaces:
  - Name\Space\Drive
```
