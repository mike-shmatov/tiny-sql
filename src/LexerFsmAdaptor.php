<?php
namespace Tiny\Sql;

class LexerFsmAdaptor
{
    private $fsm;
    
    public function __construct($fsm) {
        $this->fsm = $fsm;
    }
    
     public function comment($comment){
        
    }
    
    public function stringLiteral($literal){
        $this->fsm->token($literal);
    }
    
    public function unknown($token){
        $this->fsm->token($token);
    }
    
    public function semicolon(){
        $this->fsm->semicolon();
    }
    
    public function openSpecialComment(){
        
    }
    
    public function closeComment(){
        
    }
    
    public function token($token){
        $this->fsm->token($token);
    }
}
