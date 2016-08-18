<?php
require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

require('head.php');
?>

<div class="title1">
    <div class="txt">購物車</div>
</div>

<ul class="mui-table-view" id="list-view" style=" background-color:#fff ;">

    <li class="mui-table-view-cell mui-media">
        <a href="javascript:;">
            <img class="mui-media-object mui-pull-left" src="<%= getvarious("domain") + "uploads/pictures/product/prm" + list[i]["src"] %>">
            <div class="mui-media-body">
                <div class="jq-cart-co">
                    價 錢：$<%= list[i]["sale"] %> 
                    <span style=" margin-left:5px ;">數量：<%= list[i]["number"] %></span> 
                    <span style=" margin-left:15px ; color:#fb9879 ; font-size:14px ;" onclick="delCart(<%= list[i]["id"] %>)">删除</span>
                </div>
                <p class='mui-ellipsis'><%= list[i]["name"] %></p>
            </div>
        </a>
    </li>
    
</ul>

<div class="mui-table-view-cell mui-media" id="checkout" style="display: none;">
    <a href="checkout.html" data-action="openwindow">
        <div class="jq-cart-co" style=" float: left; line-height:35px ;">總價錢：$<span data-info="total">0.00</span></div>
        <div class="zh-good-go" style="background-color:#4ebdf4 ; float: right; text-align: center; margin-bottom: 0px;">確認下單</div>
    </a>    
</div>

<?php require('foot.php'); ?>