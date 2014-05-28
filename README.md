ArrayQuery
==========

ArrayQuery is a library to query, group and sort data stored in a nested php array format.

### Example

```php
use Asimlqt\ArrayQuery\Input\ArrayOfArrays;
use Asimlqt\ArrayQuery\Query;

$data = array(
    array(
        'continent' => 'Europe',
        'country' => 'England',
        'city' => 'London',
        'population' => '8000000'
    ),
    array(
        'continent' => 'Europe',
        'country' => 'France',
        'city' => 'Paris',
        'population' => '2200000'
    ),
    array(
        'continent' => 'Asia',
        'country' => 'Japan',
        'city' => 'Tokyo',
        'population' => '13200000'
    ),
);

$source = new ArrayOfArrays($data);
$query = new Query($source);
$query->where(function($row) { return $row['population'] > 5000000; });
print_r($query->getResult());
    
```

### API

#### select($columns)

Select is similar to a select in an sql query, It only keeps the columns which are passed to the method and discards all others. This sohuld generally be called after queryingfor the required data, just before calling getResult().  Once select has been called, you can not filter or query on the rows which have been removed by the select as they no longer exist.

```php
$query->select(array('city', 'population'));
```


#### filter($column, array $values)

Filter removes columns which don't have one of the specified values. It is similar to a "WHERE IN" clause in mysql.

```php
$query->filter('city', array('London', 'Paris'));
``` 


#### where($closure)

The where function gives the freedom of filtering data in any way the user likes. The closure accepts the current row and must return true or false. If the function returns boolean true then the row will be kept, any other value will cause the row to be removed.

```php
$query->where(function($row) { return $row['population'] > 5000000; });
```