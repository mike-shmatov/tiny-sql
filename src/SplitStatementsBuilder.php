<?php
namespace Tiny\Sql;

class SplitStatementsBuilder
{
    private $statements = [];
    private $statement = [];
    
    public function getStatements(){
        if(count($this->statement)){
            throw new \LogicException('Trying to get statements having unfinished statement');
        }
        return $this->statements;
    }
    
    public function statementDone(){
        $text = implode(' ', $this->statement);
        $this->statement = [];
        $this->statements[] = $text;
    }
    
    public function write($input){
        $this->statement[] = $input;
    }
}
