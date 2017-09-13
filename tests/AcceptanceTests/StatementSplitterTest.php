<?php
class StatementsSplitterTest extends PHPUnit_Framework_TestCase
{
    private $splitter;
    
    public function setUp(){
        $this->splitter = Tiny\Sql\Parsers\StatementsSplitter::make();
    }
    
    public function testSimpleStatement(){
        $statement = 'CREATE TABLE t(id INTEGER PRIMARY KEY AUTOINCREMENT, column TEXT);';
        $parsed = $this->splitter->parse($statement);
        $this->assertStringsEqualWithoutSpaces('CREATE TABLE t (id INTEGER PRIMARY KEY AUTOINCREMENT, column TEXT);', $parsed[0]);
        $this->assertCount(1, $parsed);
    }
    
    public function testTwoStatements(){
        $input = <<<TXT
DROP TABLE IF EXISTS `abilityattributes`;
CREATE TABLE IF NOT EXISTS `abilityattributes` (
  `accountID` int(10) unsigned NOT NULL,
  `abilityID` binary(6) NOT NULL,
  `base` enum('day','week','mixed') COLLATE utf8_bin NOT NULL,
  `howApplied` enum('daily','weekly','base') COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`accountID`,`abilityID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
TXT;
        $parsed = $this->splitter->parse($input);
        $this->assertCount(2, $parsed);
        $this->assertStringsEqualWithoutSpaces('DROP TABLE IF EXISTS `abilityattributes`;', $parsed[0]);
        $this->assertStringsEqualWithoutSpaces("CREATE TABLE IF NOT EXISTS `abilityattributes` (`accountID` int(10) unsigned NOT NULL, `abilityID` binary(6) NOT NULL, `base` enum('day','week','mixed') COLLATE utf8_bin NOT NULL, `howApplied` enum('daily','weekly','base') COLLATE utf8_bin NOT NULL, PRIMARY KEY (`accountID`,`abilityID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;", $parsed[1]);
    }
    
    public function assertStringsEqualWithoutSpaces($expected, $actual){
        $withoutSpaces = preg_replace('/\s/', '', [$expected, $actual]);
        $this->assertEquals($withoutSpaces[0], $withoutSpaces[1], 'Provided statements are not equal if taken without \s characters');
    }
}
