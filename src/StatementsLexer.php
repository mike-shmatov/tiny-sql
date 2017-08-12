<?php
namespace Tiny\Sql;

class StatementsLexer
{
    private $collector;
    private $position;
    private $patterns = [
        'whitespace' => '\s+',
        'comment' => [
            '-- .*$',
            '\/\*.*\*\/'
        ],
        'stringLiteral' => [
            '"([^"]*(?:"")*)*"(?=\s|$|;)',
            '\'.*\'(?=\\s|$)'
        ],
        'unknown' => '.*?(?=\s|$|;)',
        'singleCharacter' => ';'
    ];
    
    public function __construct($colletctor){
        $this->collector = $colletctor;
    }
    
    public function lex($input){
        $lines = mb_split("\n|\r\n", $input);
        foreach($lines as $line){
            $this->lexLine($line);
        }
    }
    
    private function lexLine($line){
        $this->position = 0;
        while($this->position < mb_strlen($line)){
            if(!$this->matchNext(mb_substr($line, $this->position))){
                $this->position++;
            }
        }
    }
    
        private function matchNext($line){
            return $this->matchWhitespace($line) ||
                    $this->matchSingleCharacter($line) ||
                    $this->matchComment($line) ||
                    $this->matchStringLiteral($line) ||
                    $this->matchUnknown($line);
        }
        
        private function matchSingleCharacter($line){
            $char = $this->match('singleCharacter', $line);
            if($char){
                switch($char){
                    case ';':
                        $this->collector->semicolon();
                        break;
                }
                return true;
            }
            else {
                return false;
            }
        }
        
        private function matchUnknown($line){
            if($unknown = $this->match('unknown', $line)){
                $this->collector->unknown($unknown);
                return true;
            }
            return false;
        }
        
        private function matchWhitespace($line){
            return $this->match('whitespace', $line);
        }
        
            private function match($name, $line, $modifiers = ''){
                //print "\nline is $line";
                $patterns = $this->patterns[$name];
                if(!is_array($patterns)){
                    $patterns = [$patterns];
                }
                foreach($patterns as $pattern){
                    mb_ereg_search_init($line);
                    if($matches = mb_ereg_search_regs('^'.$pattern, $modifiers)){
                        //var_dump($matches);
                        $token = $matches[0];
                        $this->position += mb_strlen($token);
                        //print "\nfound $token";
                        return $token;
                    }
                }
                return false;
            }
        
        private function matchComment($line){
            if($comment = $this->match('comment', $line)){
                $styles = [
                    ['(?<=-- ).*$', 0],
                    ['(?:/\*)(.*)(?:\*\/)', 1]
                ];
                foreach($styles as $pattern){
                    //print "\npattern {$pattern[0]} will try on $comment";
                    if(mb_ereg($pattern[0], $comment, $match)){
                        //var_dump($match);
                        $this->collector->comment($match[$pattern[1]]);
                        return true;
                    }
                    //var_dump($match);
                }
                
            }
            else return false;
        }
        
        private function matchStringLiteral($line){
            if($literal = $this->match('stringLiteral', $line, 'l')){
                $this->collector->stringLiteral($literal);
                return true;
            }
            return false;
        }
}