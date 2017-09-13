<?php
namespace Tiny\Sql\UnitTests\StatementsSplitterFSM;

class NewStateTest extends StateTestCase
{
    private $state;
    
    public function setUp(){
        parent::setUp();
        $this->state = new \Tiny\Sql\StatementsSplitterFSM\NewState();
        $this->state->initialize($this->fsm, $this->builder);
    }
    
    public function testTokenEvent(){
        $this->fsm->expects($this->once())
                  ->method('setState')
                  ->with('WritingState');
        $this->builder->expects($this->once())
                     ->method('write')
                     ->with('something');
        $this->state->token('something');
        
    }
    
    public function testCommentEvent(){
        $this->fsm->expects($this->never())
                  ->method('setState');
        $this->state->comment('a comment');
    }
    
    public function testSemicolonEvent(){
        $this->fsm->expects($this->never())
                  ->method('setState');
        $this->state->semicolon();
    }
    
    public function testEndEvent(){
//        $this->builder->expects($this->once())
//                      ->method('done');
//        $this->state->done();
    }
}