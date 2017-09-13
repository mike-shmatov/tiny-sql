<?php
namespace Tiny\Sql\StatementsSplitterFSM;

class NewState implements EventsInterface
{
    private $fsm;
    private $builder;
    
    public function initialize($fsm, $builder){
        $this->fsm = $fsm;
        $this->builder = $builder;
    }
    
    public function token($token){
        $this->fsm->setState('WritingState');
        $this->builder->write($token);
    }
    
    public function comment($comment){
        // no op;
    }
    
    public function semicolon(){
        // no op;
    }
    
    public function done(){
        // no op;
    }
}