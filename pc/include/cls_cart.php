<?php

class Cart {

    private $cart_goods = null;

    public $cookieName = 'newcookiecart';

    //获取购物车的信息
    public function getCart($id=null){
        global $my_db;

        $cur_goods = array();

        if ($this->cart_goods === null) {
            $cur_cart = unserialize(stripslashes($_COOKIE[$this->cookieName]));
            $cur_goods = $cur_cart;

            if ($id) {
                $cur_goods = array($id => $cur_cart[$id]);
            }

        } else {
            $cur_goods = $this->cart_goods;
        }

        $product_list = array();

        if (!empty($cur_goods)) {
            $getdata = $my_db->selectRow('id, sale, name, src, path', 'product', array('id IN (' . implode(',', array_keys($cur_goods)) . ') AND valid = 1'));
            while($result = mysql_fetch_array($getdata)) {
                $result = array_merge($result, $cur_goods[$result['id']]);
                $product_list[$result['id']] = $result;
            }
        }

        return $product_list;
    }

    //加入购物车
    public function addCart($goods=array()){
        $goods['id'] = intval($goods['id']);

        if ($this->cart_goods === null) {
            $cur_cart = unserialize(stripslashes($_COOKIE[$this->cookieName]));

        } else {
            $cur_cart = $this->cart_goods;
        }

        if($cur_cart == ''){
            $cart_info = array();
            $cart_info[$goods['id']] = $goods;
            setcookie($this->cookieName, serialize($cart_info), time() + 3600 * 24 * 2, '/');
        } 
        else if($cur_cart <> '') {
            if ($goods['num']) {
                if ($cur_cart[$goods['id']]) {
                    $cur_cart[$goods['id']] = array_merge($cur_cart[$goods['id']], $goods);

                } else {
                    $cur_cart[$goods['id']] = $goods;
                }
            }
            else {
                unset($cur_cart[$goods['id']]);
            }
            
            setcookie($this->cookieName, serialize($cur_cart), time() + 3600 * 24 * 2, '/');
        }

        $this->cart_goods = $cur_cart;

        return true;
    }

    //从购物车删除
    public function delCart($id){
        //回复序列化的数组
        $cur_goods = unserialize(stripslashes($_COOKIE[$this->cookieName]));

        unset($cur_goods[$id]);
        setcookie($this->cookieName, serialize($cur_goods), time() + 3600 * 24 * 2, '/');

        $this->cart_goods = $cur_goods;

        return true;
    }

    //清空购物车
    public function emptyCart(){
        setcookie($this->cookieName, '', time() + 3600 * 24 * 2, '/');

        $this->cart_goods = array();

        return true;
    }

}