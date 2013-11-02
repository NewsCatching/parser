<?php
function exit_r($array, $continue=false){
        echo "<xmp>"; print_r($array);
        if($continue!=true)exit();
	else echo "</xmp>";
}
?>
