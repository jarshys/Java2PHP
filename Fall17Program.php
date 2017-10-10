<html>
  <head>
    <title>Program Evaluator</title>
  </head>
  <body>
  <pre>
  
 

  </pre>
  </body>
</html>
<?php 
//MAIN CLASS WHERE CALL OTHER CLASSES AND THE METHODS

include'Lexer.php';
include'Token.php';

/*
 * @author Luis Romero for Secure Web-Based Systems, Fall 2017
 *
 * This program parses and interprets a simple programming language that has
 * only single digit assignments to variables and nested if or if-else
 * statements.
 *
 * <program> ::= <statement>* <results>
 * <statement> ::= <assignment> | <conditional>
 * <assignment> ::= ID '=' <expression>
 * <expression> ::= ID | VALUE
 * <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{'
 * <statement>* '}' ]
 * <condition> ::= '(' <expression> '<' <expression> ')' <
 * results> ::= ':' ID* ID is [a-z] (one lower case letter) VALUE is [0-9] (one
 * digit)
 *
 */
 
     $oneIndent = "   ";
     $programs = file('http://cs5339.cs.utep.edu/longpre/assignment2/programs.txt', FILE_IGNORE_NEW_LINES);
     foreach ($programs as $line){
         echo $line.PHP_EOL;//'<br>';
         $program = file_get_contents($line);
         $lex = new Lexer($program);
         $currentToken = $lex->next();
         try{
             execProgram($oneIndent);
             if($currentToken->getType() != Token::TOKEN_EOF){
                 echo "Unecpected Character at the end of the program".PHP_EOL;//'<br>';
                 throw new Exception();
             }             
         }
         catch (Exception $ex){
             echo '<br>'."Program Parsing Aborted". '<br>';
         }
         echo PHP_EOL;
     }
     // A statement starts with either ID or IF.
     // A result starts with COLON
         function execProgram($indent){
             Global $currentToken;
          
             while($currentToken->getType() == Token::TOKEN_ID || $currentToken->getType() == Token::TOKEN_IF_TAG){
                 execState($indent,True);
             }
             execResult($indent);
         }
     // An assignment starts with ID and a conditional starts with IF
         function execState($indent, $executing){
             Global $currentToken;
             
             if($currentToken->getType() == Token::TOKEN_ID){
                 execAssign($indent, $executing);
             }
             else{
                 execCondition($indent, $executing);
             }
         }
      // We know the current token is ID
         function execAssign($indent, $executing){
             Global $currentToken, $lex, $values;
            
            $c = substr($currentToken->getStr(),0,1);
             $currentToken = $lex->next();
             if($currentToken->getType() != Token::TOKEN_EQUAL){
                 echo '<br>'."Equal sign expected".PHP_EOL;//'<br>';
                 throw new Exception();
             }
             $currentToken = $lex->next();
             echo $indent.$c." = ";
             $value = execExpr($indent);
             //echo '<br>';
             echo PHP_EOL;
             if($executing){
                 $values[$c] = $value;
             }
         }
      // <conditional> ::= 'if' <condition> '{' <statement>* '}'
      //                   [ 'else' '{' <statement>* '}' ]
         function execCondition($indent, $executing){
             Global $currentToken, $lex, $oneIndent;
             echo $indent."if ";
             $currentToken = $lex->next();
             $condResult = execCond($indent);
             echo " {".PHP_EOL;//'<br>';
             if($currentToken->getType() != Token::TOKEN_LBRAKET){
                 echo "Left Bracket Expected".PHP_EOL;
                // echo "<br>";
                 throw new Exception();
             }
             $currentToken = $lex->next();
             while ($currentToken->getType() == Token::TOKEN_ID || $currentToken->getType() == Token::TOKEN_IF_TAG){
                 execState($indent.$oneIndent, !$condResult);
             }
             if($currentToken->getType() != Token::TOKEN_RBRAKET){
                 echo "Right Bracket or Statement Expected".PHP_EOL;
                // echo '<br>';
                 throw new Exception();
             }
             echo $indent."}";
             $currentToken = $lex->next();
             if($currentToken->getType() == Token::TOKEN_ELSE_TAG){
                 $currentToken = $lex->next();
                 if($currentToken->getType() != Token::TOKEN_LBRAKET){
                     echo "Left Braket Expected".PHP_EOL;
                     //echo'<br>';
                     throw new Exception();
                 }
                 $currentToken = $lex->next();
                 echo " else {".PHP_EOL;//'<br>';
                while ($currentToken->getType() != Token::TOKEN_ID || $currentToken->getType() == Token::TOKEN_IF_TAG){
                     execState($indent.$oneIndent, !$condResult);
                 }
                 if($currentToken->getType() != Token::TOKEN_RBRAKET){
                     //PROBLEM HERE*******
                     echo "Right Bracket or Statement Expected".PHP_EOL;
                     throw new Exception();
                 }
                 echo $indent."}";
                 $currentToken = $lex->next();
             }
             echo PHP_EOL;//'<br>';
            
         }
      // <condition>   ::= '(' <expression> '<' <expression> ')'
         function execCond($indent){
             Global $currentToken, $lex;
             if($currentToken->getType() != Token::TOKEN_LPARENT){
                 echo"Left Parenthesis Expected".PHP_EOL;
                 //echo'<br>';
                 throw new Exception();
             }
             echo"(";
             $currentToken = $lex->next();
             $v1 = execExpr($indent);
             if($currentToken->getType() != Token::TOKEN_LESS){
                 echo "LESS THAN expected".PHP_EOL;
                 //echo '<br>';
                 throw new Exception();
             }
             echo "&lt;";
             $currentToken = $lex->next();
             $v2 = execExpr($indent);
             if($currentToken->getType() != Token::TOKEN_RPARENT){
                 echo "Right Parenthesis Expected".PHP_EOL;
                // echo'<br>';
                 throw new Exception();
             }
             echo ")";
             $currentToken = $lex->next();
             return $v1 < $v2;             
         }
      // <expression>  ::= ID | VALUE
         function execExpr($indent){
             global $currentToken, $lex, $values;
             if($currentToken->getType() == Token::TOKEN_VALUE){
                 $val = $currentToken->getVal();
                 echo $val;
                 $currentToken = $lex->next();
                 return $val;
             }
             if($currentToken->getType() == Token::TOKEN_ID){
                $c = substr($currentToken->getStr(),0,1);
                 echo  $c;
                 if(array_key_exists($c, $values)){
                     $currentToken = $lex->next();
                     return $values[$c];
                 }
                 else{
                     echo "Reference to an Undefined Varible".PHP_EOL;
                    // echo'<br>';
                     throw new Exception();
                 }
             }
             echo "An Expression Should Be Either a Digit or a Letter".PHP_EOL;
             //echo '<br>';
             throw new Exception();
         }
      // <results>     ::= ':' ID*
         function execResult($indent){
             global $currentToken, $lex, $values;
            if($currentToken->getType() != Token::TOKEN_COLON){
                echo "COLON or Statement Expected".PHP_EOL;//<br>";
                 throw new Exception();
             }
             $currentToken = $lex->next();
             while($currentToken->getType() == Token::TOKEN_ID){
                 $c = substr($currentToken->getStr(),0,1);
                 $currentToken = $lex->next();
                 if(array_key_exists($c, $values)){  
                     echo "The Value of ". $c ." is ".$values[$c].PHP_EOL;
                     echo PHP_EOL;//'<br>';
                 }
                 else {
                     //echo '<br>';
                     echo "The Value of ". $c." is Undefined ".PHP_EOL;
                     //echo '<br>';
                 }
             }
             
         }
         
?>         
  </pre>
 </body>
</html>