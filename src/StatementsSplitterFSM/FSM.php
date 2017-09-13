<?php
namespace Tiny\Sql\StatementsSplitterFSM;

class FSM implements EventsInterface
{
    private $states;
    private $currentState;
    
    public function __construct(array $states, $builder){
        $this->states = $states;
        foreach($this->states as $state){
            $state->initialize($this, $builder);
        }
        $this->currentState = 'NewState';
    }
    
    public function setState($name){
        $this->currentState = $name;
    }
    
    public function comment($comment) {
        $this->states[$this->currentState]->comment($comment);
    }

    public function done() {
        $this->states[$this->currentState]->done();
    }

    public function semicolon() {
        $this->states[$this->currentState]->semicolon();
    }

    public function token($token) {
        $this->states[$this->currentState]->token($token);
    }
}
