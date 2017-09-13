<?php
namespace Tiny\Sql\UnitTests\StatementsSplitterFSM;

class StateTestCase extends \PHPUnit_Framework_TestCase
{
    protected $fsm;
    protected $builder;
    
    public function setUp(){
        $this->fsm = $this->getMockBuilder('Tiny\\Sql\\StatementsSplitterFSM\\StateMachine')
                          ->setMethods(['setState'])
                          ->getMock();
        $this->builder = $this->getMockBuilder('Tiny\\Sql\\StatementsSplitter\\Builder')
                             ->setMethods(['write', 'statementDone'])
                             ->getMock();
    }
}
