<?php
class StatementsLexerTest extends PHPUnit_Framework_TestCase
{
    private $lexer;
    private $collector;
    private $collected;
    
    public function collected(){
        return implode(';', $this->collected);
    }
    
    public function setUp(){
        $this->collector = $this;
        $this->lexer = new Tiny\Sql\StatementsLexer($this->collector);
        $this->collected = [];
    }
    
    /**
     * @test
     */
    public function canParseSingleLineComment(){
        $this->lexer->lex('-- A comment');
        $this->assertEquals('REM:A comment', $this->collected());
    }
    
    /**
     * @test
     */
    public function canParseTwoLinesComments(){
        $this->lexer->lex('-- First line'.PHP_EOL.'-- Second line');
        $this->assertEquals('REM:First line;REM:Second line', $this->collected());
    }
    
    /**
     * @test
     */
    public function canDoSimpleStringLiteral(){
        $this->lexer->lex('"string"');
        $this->assertEquals('STR:"string"', $this->collected());
    }
    
    /**
     * @test
     */
    public function canDoSeveralLiterals(){
        $this->lexer->lex('"string"'.' '."'another'".' '); // "string" 'another'
        $this->assertEquals('STR:"string";STR:\'another\'', $this->collected());
    }
    
    /**
     * @test
     */
    public function canDoSpecialStringLiterals(){
        $this->lexer->lex('"double""Quote"'.' '."'single''quote'");
        $this->assertEquals('STR:"double""Quote";STR:\'single\'\'quote\'', $this->collected());
    }
    
    /**
     * @test
     */
    public function canMatchBadStringAsUnknown(){
        $this->lexer->lex('"double"Quote"'.";'single'quote'");
        $this->assertEquals('#"double"Quote";SC;#\'single\'quote\'', $this->collected());
    }
    
    /**
     * @test
     */
    public function canMatchSingleCharacters(){
        $this->lexer->lex('"string";');
        $this->assertEquals('STR:"string";SC', $this->collected());
    }
    
    /**
     * @test
     */
    public function canDoSetStatement(){
        $this->lexer->lex("SET FOREIGN_KEY_CHECKS=0;");
        $this->assertEquals('#SET;#FOREIGN_KEY_CHECKS;#=0;SC', $this->collected());
    }
    
    /**
     * @test
     */
    public function canMatchSlashAsteriskComment(){
        $this->lexer->lex("/*a comment*/");
        $this->assertEquals('REM:a comment', $this->collected());
    }
    
    /**
     * @test
     */
    public function canMatchMultilineComments(){
        $this->lexer->lex("token;/*a"."\n"."comment*/token;");
        $this->assertEquals('#token;SC;REM:a;REM:comment;#token;SC', $this->collected());
    }
    
    /**
     * @test
     */
    public function canMatchSpecialComments(){
        $this->lexer->lex("/*!some statement*/;");
        $this->assertEquals('SCopen;#some;#statement;Cclose;SC', $this->collected());
    }
    
    /**
     * @test
     */
    public function canMatchRealStatement(){
        $this->lexer->lex('CREATE DATABASE IF NOT EXISTS `db` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;');
        $this->assertEquals('#CREATE;#DATABASE;#IF;#NOT;#EXISTS;#`db`;#DEFAULT;#CHARACTER;#SET;#utf8;#COLLATE;#utf8_bin;SC', $this->collected());
    }
    
    public function testWithNewlined(){
        $this->lexer->lex('`abilityattributes`;'."\n".'CREATE');
        $this->assertEquals('#`abilityattributes`;SC;#CREATE', $this->collected());
    }
    
    /**
     * This and further are methods to collect results from lexer
     */
    public function comment($comment){
        $this->collected[] = 'REM:'.$comment;
    }
    
    public function stringLiteral($literal){
        $this->collected[] = 'STR:'.$literal;
    }
    
    public function unknown($token){
        $this->collected[] = '#'.$token;
    }
    
    public function semicolon(){
        $this->collected[] = 'SC';
    }
    
    public function openSpecialComment(){
        $this->collected[] = 'SCopen';
    }
    
    public function closeComment(){
        $this->collected[] = 'Cclose';
    }
    
    public function token($token){
        $this->collected[] = '#'.$token;
    }
}