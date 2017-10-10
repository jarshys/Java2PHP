

<?php

Class Lexer{

    private $program;             //CHAR[]
    private $i=0;                //INT
    private $length;            //INT
    const LETTERS = "abcdefghijklmnopqrstuvwxyz";  //CONSTANT
    const NUMBERS = "1234567890";       //CONSTANT
    
   function __construct($s){
       $this->length = strlen($s);
       $this->program = str_split($s);
    }

    function next(){
        
        while($this->i < count($this->program) && (strpos(" ".PHP_EOL, $this->program[$this->i]) !== false)){
            $this->i++;
        }
        
        if($this->i >= count($this->program)){ 
            return new Token(Token::TOKEN_EOF,"",0);
        }
        switch ($this->program[$this->i]){
            case'(':
                $this->i++;
                return new Token(Token::TOKEN_LPARENT,"(",0);
            case')':
                $this->i++;
                return new Token(Token::TOKEN_RPARENT,")",0);
            case'{':
                $this->i++;
                return new Token(Token::TOKEN_LBRAKET,"{",0);
            case'}':
                $this->i++;
                return new Token(Token::TOKEN_RBRAKET,"}",0);
            case '<':
                $this->i++;
                return new Token(Token::TOKEN_LESS,"<",0);
            case '=':
                $this->i++;
                return new Token(Token::TOKEN_EQUAL,"=",0);
            case':':
                $this->i++;
                return new Token(Token::TOKEN_COLON,":",0);
        }
        $c = $this->program[$this->i];
        if(strpos(self::NUMBERS,$c) !== false){
            $this->i++;
            return new Token(Token::TOKEN_VALUE, $c, intval($c));
        }
        if(strpos(self::LETTERS, $c) !== false){
            $id = "";
            while($this->i < count($this->program) && strpos(self::LETTERS, $this->program[$this->i]) !== false){
                $id = $id.$this->program[$this->i];
                $this->i++;
            }
            if(strcmp("if", $id) === 0){
                return new Token(Token::TOKEN_IF_TAG,"if",0);
            }
            if (strcmp("else", $id) === 0){
                return new Token(Token::TOKEN_ELSE_TAG,"else",0);
            }
            if(strlen($id) == 1){  
                return new Token(Token::TOKEN_ID,$id,0);
            }
            
            return new Token(Token::TOKEN_INVALID,"",0);
        }
        return new Token(Token::TOKEN_INVALID,"",0);
    }   
}
?>