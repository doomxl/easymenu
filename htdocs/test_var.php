<?php

    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    $array_var = array( "menu1" => "salade de chevere", "menu2" => "ravioli au calamar", "item1" => "tomates", "item2" => "patates");

    print("<pre>");
    print_r($array_var);
    print("</pre>");

    $array_menu = array();

    print("***  menu  ***<br/><br/>");

    foreach ($array_var as $key => $value) {
        if(substr($key,0,-1) == "menu") {
            print($value."<br/>");
            $array_menu[] = $value;   
        }
    }

    print("<pre>");
    print_r($array_menu);
    print("</pre>");

    $array_item = array();

    print("<br/>***  item  ***<br/><br/>");

    foreach ($array_var as $key => $value) {
        if(substr($key,0,-1) == "item") {
            print($value."<br/>");
            $array_item[] = $value; 
        }
    }

    print("<pre>");
    print_r($array_item);
    print("</pre>");

    print("<br/>*** end lineaaire ***<br/><br/>");
    
    
    $array_foo = array ("menu","item");
    
    
    
    
    foreach ($array_foo as $key => $value1) {
    
        $array_name = "array_".$value1;
        $$array_name = array();

        print("<br/>###  $value1  ###<br/><br/>");

        foreach ($array_var as $key => $value) {
            if(substr($key,0,-1) == $value1) {
                print($value."<br/>");
                ${$array_name}[] = $value; 
            }
        }
        
        //$$array_name = $array_tmp;
        
        print("<pre>");
        print_r($$array_name);
        print("</pre>");
        
    }

?>
