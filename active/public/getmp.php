<?php
/**
 * Created by PhpStorm.
 * @User: 嘿嘿<skwordss@163.com>
 * Date: 2019/8/3 0003
 * Time: 16:29
 */

$mp3  = file_put_contents("./c.mp3",file_get_contents("http://mp3.9ku.com/hot/2011/03-04/409218.mp3"));
dump($mp3);