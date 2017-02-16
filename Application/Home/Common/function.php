<?php 

// 说说时间显示
function post_time($param)
{
    if (date('Y', $param) != date("Y")) {
        return date('Y-m-d', $param);
    } elseif (date('Y-m-d', $param) == date("Y-m-d")) {
        return date('H:i', $param);
    } else {
        return date('m-d', $param);
    }
}
