<?php
namespace Tiny\Sql\Parsers;

class StatementsSplitter
{
    private $builder;
    private $lexer;
    
    public function __construct(){
        $this->builder = new \Tiny\Sql\SplitStatementsBuilder();
        $states = [
            'NewState' => new \Tiny\Sql\StatementsSplitterFSM\NewState(),
            'WritingState' => new \Tiny\Sql\StatementsSplitterFSM\WritingState()
        ];
        $fsm = new \Tiny\Sql\StatementsSplitterFSM\FSM($states, $this->builder);
        $adaptor = new \Tiny\Sql\LexerFsmAdaptor($fsm);
        $this->lexer = new \Tiny\Sql\StatementsLexer($adaptor);
    }
    
    public function parse($source){
        $this->lexer->lex($source);
        return $this->builder->getStatements();
    }
    
    public static function make(){
        return new self();
    }
}
