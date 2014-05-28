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

