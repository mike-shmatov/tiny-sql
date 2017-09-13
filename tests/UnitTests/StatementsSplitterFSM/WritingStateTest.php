<?php
namespace Tiny\Sql\UnitTests\StatementsSplitterFSM;

class WritingStateTest extends StateTestCase
{
    private $state;
    
    public function setUp(){
        parent::setUp();
        $this->state = new \Tiny\Sql\StatementsSplitterFSM\WritingState();
        $this->state->initialize($this->fsm, $this->builder);
    }
    
    public function testTokenEvent(){
        $this->builder->expects($this->once())
                      ->method('write')
                      ->with('token');
        $this->state->token('token');
    }
    
    public function testSemicolonEvent(){
        $this->fsm->expects($this->once())
                  ->method('setState')
                  ->with('NewState');
        $this->builder->expects($this->once())
                      ->method('write')
                      ->with(';');
        $this->builder->expects($this->once())
                      ->method('statementDone');
        $this->state->semicolon();
    }
    
    public function testDoneThrows(){
        $this->expectException(\LogicException::class);
        $this->state->done();
    }
}
