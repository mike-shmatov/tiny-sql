<?php
namespace Tiny\Sql\UnitTests\StatementsSplitterFSM;

class FSMTest extends \PHPUnit_Framework_TestCase
{
    private $fsm;
    private $builder;
    private $newState;
    private $writingState;
    
    public function setUp(){
        $this->newState = $this->getMockBuilder(\Tiny\Sql\StatementsSplitterFSM\EventsInterface::class)
                               ->setMethods(['initialize', 'comment', 'token', 'semicolon', 'done'])
                               ->getMock();
        $this->writingState = $this->getMockBuilder(\Tiny\Sql\StatementsSplitterFSM\EventsInterface::class)
                               ->setMethods(['initialize', 'comment', 'token', 'semicolon', 'done'])
                               ->getMock();
        $states = [
            'NewState' => $this->newState,
            'WritingState' => $this->writingState
        ];
        $this->builder = $this->getMockBuilder('Tiny\\Sql\\StatementsSplitter\\Builder')
                             ->setMethods(['write', 'statementDone'])
                             ->getMock();
        $this->fsm = new \Tiny\Sql\StatementsSplitterFSM\FSM($states, $this->builder);
    }
    
    public function testStatesGetInitialized(){
        $states = [
            'newState' => $this->newState,
            'writingState' => $this->writingState
        ];
        $this->builder = $this->getMockBuilder('Tiny\\Sql\\StatementsSplitter\\Builder')
                             ->setMethods(['write', 'statementDone'])
                             ->getMock();
        $this->newState->expects($this->once())
                       ->method('initialize')
                       ->with($this->callback(function($fsm){
                           return $fsm instanceof \Tiny\Sql\StatementsSplitterFSM\FSM;
                       }));
        $this->fsm = new \Tiny\Sql\StatementsSplitterFSM\FSM($states, $this->builder);
    }
    
    public function testInitialStateIsNewState(){
        $this->newState->expects($this->once())
                       ->method('done');
        $this->fsm->done();
    }
    
    /**
     * @dataProvider interfaceMethodsDataProvider
     */
    public function testAllMethodsAreDelegatedToState($method, $argument){
        if(!is_null($argument)){
            $this->newState->expects($this->once())
                       ->method($method)
                       ->with($argument);
            $this->fsm->{$method}($argument);
        }
        else {
            $this->newState->expects($this->once())
                       ->method($method);
            $this->fsm->{$method}();
        }
    }
    
        public function interfaceMethodsDataProvider(){
            return [
                ['done', NULL],
                ['token', 'token'],
                ['semicolon', NULL],
                ['comment', 'comment']
            ];
        }
        
    public function testSetState() {
        $this->fsm->setState('WritingState');
        $this->writingState->expects($this->once())
                           ->method('token')
                           ->with('token');
        $this->fsm->token('token');
    }
}
