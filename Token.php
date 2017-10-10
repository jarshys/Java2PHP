<?php

class Token{
    //ENUM TOKENLIST TRANSLATE TO CONSTANTANTS
    const TOKEN_RPARENT = 0;    //')';
    const TOKEN_LPARENT = 1;    //'(';
    const TOKEN_RBRAKET = 2;    //'{';
    const TOKEN_LBRAKET = 3;    //'}';
    const TOKEN_EQUAL = 4;     //'=';
    const TOKEN_LESS = 5;      //'<';
    const TOKEN_COLON = 6;     //':';
    const TOKEN_ID = 7;        //'ID'; //a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z
    const TOKEN_VALUE = 8;     //'Value'; //0,1,2,3,4,5,6,7,8,9
    const TOKEN_IF_TAG = 9;         //'IF';
    const TOKEN_ELSE_TAG = 10;      //'ELSE';
    const TOKEN_EOF = 11;      //':';
    const TOKEN_INVALID = 12;   
    //VARIABLES FROM THE TOKEN
    public $type = self::TOKEN_INVALID;      //TokenList
    public $val=0;       //Integer
    public $str="";       //String

   function __construct($theType,$theString,$theVal){
       $this->type=$theType;
       $this->str=$theString;
       $this->val=$theVal;
   }
   function getType(){
       return $this->type;
   }
   function getVal(){
       return $this->val;
   }
   function getStr(){
       return $this->str;
   }
}
?>