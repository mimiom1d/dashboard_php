<?php

//** to sanitize the user input with htmlspecialchars and trim
function safeinputs($string){
    return trim(htmlspecialchars($string, ENT_QUOTES, "UTF-8"));
}

?>