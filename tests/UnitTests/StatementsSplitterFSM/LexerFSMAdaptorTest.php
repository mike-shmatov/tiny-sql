<?php
namespace Tiny\Sql\UnitTests\StatementsSplitterFSM;

class LexerFSMAdaptorTest extends \PHPUnit_Framework_TestCase
{
    private $fsm;
    private $adaptor;
    
    public function setUp(){
        $this->fsm = $this->createMock(\Tiny\Sql\StatementsSplitterFSM\EventsInterface::class);
        $this->adaptor = new \Tiny\Sql\LexerFsmAdaptor($this->fsm);
    }
    
    public function testToken(){
        $this->fsm->expects($this->once())
                  ->method('token')
                  ->with('token');
        $this->adaptor->token('token');
    }
    
    public function testSemicolon(){
        $this->fsm->expects($this->once())
                  ->method('semicolon');
        $this->adaptor->semicolon();
    }
    
    public function testUnknown(){
        $this->fsm->expects($this->once())
                  ->method('token')
                  ->with('token');
        $this->adaptor->unknown('token');
    }
    
    public function testStringLiteral(){
        $this->fsm->expects($this->once())
                  ->method('token')
                  ->with('token');
        $this->adaptor->stringLiteral('token');
    }
}
