# SecureMy

SecureMy is a MySQL query builder, with focus on security. When using SecureMy, it should
not be possible, to create an sql injection vulnerability.

## basic usage

```
$qb = QueryBuilder::create();
$qb = $qb
    ->from('products', 'p')
    ->join(
        'product_categories',
        $qb->eq(
            $qb->column('pc.productId'),
            $qb->column('p.productId')
        ),
        'pc'
    )
    ->join(
        'categories',
        $qb->eq(
            $qb->column('pc.categoryId'),
            $qb->column('c.categoryId')
        ),
        'c'
    )
    ->groupBy('p.productId')
    ->select('p.productId', 'id')
    ->select($qb->func('count', '*'));
    
$build = $qb->build();
$stmt = $pdo->prepare($build->getQuery());
$stmt->execute($build->getParams());
```

## examples of security vulnerabilities

### sql-injection through colmn names etc.

Sometimes developer think it's a good idea, to make columns etc. dynamic, based on user input.
This can be very risky, because databases and PDO do not support passing table or column names as
parameters.

This would be the ideal solution (but unfortunately it's not supported)

```
$pdo = new PDO(...);
$query = "
    SELECT :column
    FROM producttable
    WHERE id = :id
";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'column' => $_POST['column'],
    'id' => $_POST['id'],
]);
```

I often see very risky implementations that could, if not carefully applied, cause sql injection
vulnerabilities. SecureMy protectect identifier through an character whitelist. Therefore it checks
 every identifier through the regex `/^[a-z0-9._ ]+$/i`.
 As you maybe noticed, this is not compatible to databases, which contain special character in table
 or column names. See "Cons".
 
 
### sql-injection through conditions

Most query builder allow doing something like this:

```
$qb = QueryBuilder::create();
$qb
    ->from('products')
    ->where("products.name = 'shirt'"); // most libs recomment doing ->where('roducts.name = :name') but none I found, ensures this
``` 

This is not secure, as this could result in very dangerous sql-injection vulnerabilities.
Imagine an unexperienced developer doing this:

```
$qb = QueryBuilder::create();
$qb
    ->from('products')
    ->where("products.name = {$_GET['productName']}");
```

You cannot walk into this trap with SecureMy. SecureMy prevents you from doing such crap. This comes
with a little trade of with regard to code verbosity:

```
$qb = QueryBuilder::create();
$qb
    ->from('products')
    ->where(
        $qb->eq($qb->column('products.name'), $_GET['productName'])
    );
```

## pros and cons (compared to other query builder)

### pros

- 100% secure
- immutable query builder
- works without existing connection

### cons

- more verbose
- not compatible to table-, column-, view- or sp-names containing special characters
