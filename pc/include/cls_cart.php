<?php

class Cart {

    private $cart_goods = null;

    public $cookieName = 'newcookiecart';

    //获取购物车的信息
    public function getCart($id=null){
        if ($this->cart_goods === null) {
            $cur_cart_array = unserialize(stripslashes($_COOKIE[$this->cookieName]));

            if ($id === null) {
                return $cur_cart_array;
            }
            else {
                return $cur_cart_array[$id];
            }
        }
        else {
            return $this->cart_goods;
        }
    }

    //加入购物车
    public function addCart($goods=array()){                                       //购物车的商品id和购物车的商品数量
        $cur_cart_array = unserialize(stripslashes($_COOKIE[$this->cookieName]));

        if($cur_cart_array == ''){
            $cart_info = array();
            $cart_info[$goods['id']] = $goods;
            setcookie($this->cookieName, serialize($cart_info), time() + 3600 * 24 * 2, '/');
        } 
        elseif($cur_cart_array <> '') {
            if ($goods['num']) {
                $cur_cart_array[$goods['id']] = $goods;
            }
            else {
                unset($cur_cart_array[$goods['id']]);
            }
            
            setcookie($this->cookieName, serialize($cur_cart_array), time() + 3600 * 24 * 2, '/');
        }

        $this->cart_goods = $cur_cart_array;

        return true;
    }

    //从购物车删除
    public function delCart($id){
        //回复序列化的数组
        $cur_goods_array = unserialize(stripslashes($_COOKIE[$this->cookieName]));

        unset($cur_goods_array[$id]);
        setcookie($this->cookieName, serialize($cur_goods_array), time() + 3600 * 24 * 2, '/');

        $this->cart_goods = $cur_cart_array;

        return true;
    }

    //清空购物车
    public function emptyCart(){
        setcookie($this->cookieName, '', time() + 3600 * 24 * 2, '/');

        $this->cart_goods = array();

        return true;
    }

}