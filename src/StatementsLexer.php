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
//            '\/\*[^\r^\n]*\*\/'
        ],
        'multilineComments' => '\/\*.*\*\/',
        'specialConstucts' => [
            '\/\*!',
            '\*\/'
        ],
        'stringLiteral' => [
            '"([^"]*(?:"")*)*"(?=\s|$|;)',
            "'([^']*(?:'')*)*'(?=\\s|$|;)"
        ],
        'token' => [
            '\w[\w\d]*(?=\s|$|;|\W)'
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
                    $this->matchSpecialConstruct($str) ||
                    $this->matchMultilineComments($str) ||
                    $this->matchStringLiteral($str) ||
                    $this->matchToken($str) ||
                    $this->matchUnknown($str);
        }
            
            private function matchWhitespace($line){
                return $this->match('whitespace', $line);
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
                    }
                    while (mb_ereg_search_getpos() < mb_strlen($comments));
                    return true;
                }
                else {
                    return false;
                }
            }
            
            private function matchSpecialConstruct($line){
                $special = $this->match('specialConstucts', $line, '');
                if($special){
                    switch($special){
                        case '/*!':
                            $this->collector->openSpecialComment();
                            break;
                        case '*/':
                            $this->collector->closeComment();
                            break;
                    }
                    return true;
                }
                else {
                    return false;
                }
            }
            
            private function matchToken($line){
                if($token = $this->match('token', $line)){
                    $this->collector->token($token);
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

            private function matchComment($line){
                if($comment = $this->match('comment', $line)){
                    $styles = [
                        ['(?:-- )([^\r^\n]*)(?=[\r\n]+|$)', 1, ''],
                        ['(?:/\*)(.*)(?:\*\/)', 1, '']
                    ];
                    foreach($styles as $pattern){
                        mb_ereg_search_init($comment);
                        if($match = mb_ereg_search_regs($pattern[0])){
                            $this->collector->comment($match[$pattern[1]]);
                            return true;
                        }
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

                private function match($name, $line, $modifiers = ''){
                    $patterns = $this->patterns[$name];
                    if(!is_array($patterns)){
                        $patterns = [$patterns];
                    }
                    foreach($patterns as $pattern){
                        mb_ereg_search_init($line);
                        if($matches = mb_ereg_search_regs('\A'.$pattern, $modifiers)){
                            $token = $matches[0];
                            $this->position += mb_strlen($token);
                            return $token;
                        }
                    }
                    return false;
                }
}