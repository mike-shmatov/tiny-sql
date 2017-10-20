<?php
class SplitStatementsBuilderTest extends PHPUnit_Framework_TestCase
{
    private $builder;
    
    public function setUp(){
        $this->builder = new Tiny\Sql\SplitStatementsBuilder();
    }
    
    public function testStatementsAreEmptyInitially(){
        $statements = $this->builder->getStatements();
        self::assertCount(0, $statements);
    }
    
    public function testWriting(){
        $this->builder->write('some statement');
        $this->builder->statementDone();
        $this->assertCount(1, $this->builder->getStatements());
    }
    
    public function testWritingMultipleSteps(){
        $this->builder->write('some');
        $this->builder->write('statement');
        $this->builder->statementDone();
        $this->assertCount(1, $this->builder->getStatements());
        $this->assertSame('some statement', $this->builder->getStatements()[0]);
    }
    
    public function testWritingMultipleStatements(){
        $this->builder->write('statement 1');
        $this->builder->statementDone();
        $this->builder->write('statement 2');
        $this->builder->statementDone();
        $this->assertCount(2, $this->builder->getStatements());
        $this->assertSame('statement 1', $this->builder->getStatements()[0]);
        $this->assertSame('statement 2', $this->builder->getStatements()[1]);
    }
    
    public function testExceptionOnGetStatemtnsWithoutDone(){
        $this->builder->write('part');
        $this->expectException(\LogicException::class);
        $this->builder->getStatements();
    }
    
    public function testResettingBuilder(){
        $this->builder->write('statement 1');
        $this->builder->statementDone();
        $this->builder->reset();
        $this->builder->write('statement 2');
        $this->builder->statementDone();
        $this->assertCount(1, $this->builder->getStatements());
        $this->assertSame('statement 2', $this->builder->getStatements()[0]);
    }
}
