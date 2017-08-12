<?php
namespace Tiny\Sql;

class StatementsLexer
{
    private $collector;
    private $position;
    private $patterns = [
        'whitespace' => '\s+',
        'comment' => [
            '-- .*[\r\n]*',
            '\/\*[^\r^\n]*\*\/'
        ],
        'multilineComments' => '\/\*.*\*\/',
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
        $this->position = 0;
        while($this->position < mb_strlen($input)){
            if(!$this->matchNext(mb_substr($input, $this->position))){
                $this->position++;
            }
        }
    }
    
        private function matchNext($str){
            return $this->matchWhitespace($str) ||
                    $this->matchSingleCharacter($str) ||
                    $this->matchComment($str) ||
                    $this->matchMultilineComments($str) ||
                    $this->matchStringLiteral($str) ||
                    $this->matchUnknown($str);
        }
        
        private function matchMultilineComments($line){
            $comments = $this->match('multilineComments', $line, 'm');
            if($comments){
                mb_ereg('(?:\/\*)(.*[\r\n]*.*)(?:\*\/)', $comments, $match);
                $comments = $match[1];
                mb_ereg_search_init($comments, '(.*)(?:[\r\n]*|$)', '');
                do{
                    mb_ereg_search();
                    if($match = mb_ereg_search_getregs()){
                        $this->collector->comment($match[1]);
                    }
                }while (mb_ereg_search_getpos() < mb_strlen($comments));
                return true;
            }
            else {
                return false;
            }
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
                print "\nmatching $name in $line";
                //print "\nin hex it is".(bin2hex($line));
                $patterns = $this->patterns[$name];
                if(!is_array($patterns)){
                    $patterns = [$patterns];
                }
                foreach($patterns as $pattern){
                    mb_ereg_search_init($line);
                    if($matches = mb_ereg_search_regs('^'.$pattern, $modifiers)){
                        var_dump($matches);
                        $token = $matches[0];
                        $this->position += mb_strlen($token);
                        print "\nfound $token";
                        //var_dump(bin2hex($token));
                        return $token;
                    }
                }
                return false;
            }
        
        private function matchComment($line){
            if($comment = $this->match('comment', $line)){
                $styles = [
                    ['(?:-- )([^\r^\n]*)(?=[\r\n]+|$)', 1, ''],
                    ['(?:/\*)(.*)(?:\*\/)', 1, '']
                ];
                foreach($styles as $pattern){
                    print "\npattern {$pattern[0]} will try on $comment - ". bin2hex($comment);
                    mb_ereg_search_init($comment);
                    if($match = mb_ereg_search_regs($pattern[0])){
                        var_dump(bin2hex($match[1]));
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