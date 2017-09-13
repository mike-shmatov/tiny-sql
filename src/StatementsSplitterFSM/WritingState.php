<?php
namespace Tiny\Sql\StatementsSplitterFSM;

class WritingState implements EventsInterface
{
    private $builder;
    private $fsm;
    
    public function initialize($fsm, $builder){
        $this->builder = $builder;
        $this->fsm = $fsm;
    }
    
    public function token($token){
        $this->builder->write($token);
    }
    
    public function semicolon(){
        $this->fsm->setState('NewState');
        $this->builder->write(';');
        $this->builder->statementDone();
    }

    public function comment($comment) {
        
    }

    public function done() {
        throw new \LogicException('Unexpected \'done\' event when in \'WritingState\'.');
    }
}
