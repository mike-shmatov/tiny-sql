# Tiny/Sql

## About
Currently package has single useful component with functionality to split string with multiple SQL statements into array of separate statements. Being developed mostly as helper for Tiny/DbUnit project.

## Example
```php
$inputString = 'CREATE TABLE tbl (col TEXT); INSERT INTO tbl (col) VALUES ("one;");';
$splitter = Tiny\Sql\Parsers\StatementsSplitter::make();
$statements = $this->splitter->parse($inputString);
foreach($statements as $statement){
    print $statement."\n";
}
// CREATE TABLE tbl (col TEXT ) ;
// INSERT INTO tbl ( col ) VALUES ( "one;" ) ;
```
