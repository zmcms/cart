<?php
namespace Zmcms\Cart\Frontend\Controllers;
class ZmcmsCartController{
    private $cart;
    private $totals;
    private $customer;
    public function __construct(){
        $this->cart_init();
        if(isset($_SESSION['cart_customer'])){
            $this->totals = $_SESSION['cart_customer'];
        }else{
            $this->customer = [];
        }
        

        $this->customer = [];
    }
    /** 
     * DODAJEMY DO KOSZYKA
     */
    public function put($id, $name, $q, $net, $vat, $brut, $img = null){
        $result = false;
        $cart = $this->cart; 
        if(!isset($cart[$id])){ //Gdy nie ma prduktu w koszyku
            $cart[$id] = [
                'id'=>$id,
                'name'=>$name,
                'q'=>$q,
                'net'=>$net,
                'vat'=>$vat,
                'brut'=>$brut,
                'img'=>$img,
            ];
            $this->cart_save($cart);
        }else{ // Gdy produkt już jest w koszyku
            $this->increase($id, $q);
        }
        $result = true;
        return $result;
    }
    /** 
     * DODAJEMY KOLEJNY PROFUKT DO JUŻ WŁOŻONEGO DO KOSZYKA
     */
    public function increase($id, $q){
        $cart = $this->cart;
        if(!isset($cart[$id])) return false;
        $cart[$id]['q'] = $cart[$id]['q'] + $q;
        $this->cart_save($cart);
        return true;
    }
    /** 
     * WYJMUJEMY Z KOSZYKA KILKA PRODUKTÓW Z POZYCJI, ALE NIEKONIECZNIE WSZYSTKIE
     */
    public function decrease($id, $q){
        $cart = $this->cart;
        if(!isset($cart[$id])) return false;
        $cart[$id]['q'] = $cart[$id][$q] - $q;
        if($cart[$id][$q]<0) unset($cart[$id]);
        $this->cart_save($cart);
        return true;
    }

    /** 
     * AKTUALIZUJEMY Z GÓRY ILOSĆ DANEGO PRODUKTU W KOSZYKU
     */
    public function update($id, $q){
        $cart = $this->cart;
        if(!isset($cart[$id])) return false;
        $cart[$id]['q'] = $q;
        if($cart[$id][$q]<0) unset($cart[$id]);
        $this->cart_save($cart);
        return true;
    }

    /** 
     * USUWAMY Z KOSZYKA WIERSZ PRODUKTÓW
     */
    public function delete($id){
        $cart = $this->cart;
        if(!isset($cart[$id])) return false;
        unset($cart[$id]);
        $this->cart_save($cart);
        return true;
    }
    /** 
     * Czyścimy cały koszyk
     */
    public function purge(){
        $this->cart = [];
        $this->totals =  $_SESSION['cart_totals'];
        unset($_SESSION['cart']);
        unset($_SESSION['cart_totals']);
        $this->cart_init();
        return true;
    }

    /** 
     * INICJALIZACJA KOSZYKA
     */
    private function cart_init(){
        if(isset($_SESSION['cart'])){$this->cart = $_SESSION['cart'];}else{$this->cart = [];} 
        if(isset($_SESSION['cart_totals'])){
            $this->totals = $_SESSION['cart_totals'];
        }else{
            $this->totals = [
                'net' => 0,
                'vat' => 0,
                'brut' => 0,
                'rows' => 0,
                'items_count' => 0,
            ];
        } 
    }    
    /** 
     * ZAPISANIE STANU KOSZYKA
     */
    private function cart_save($cart){
        $this->cart = $_SESSION['cart'] = $cart;
        $_SESSION['cart_totals'] = $this->cart_totals_update();
    }
    /**
     * AKTUALIZACJA DANYCH ZBIORCZYCH
     */
    public function cart_totals_update(){
        $totals['net'] = $totals['vat'] = $totals['brut']  = $totals['rows'] = $totals['items_count'] = 0;
        foreach($this->cart as $r){
            $totals['net'] = $totals['net'] + ($r['net'] * $r['q']);
            $totals['vat'] = $totals['vat'] + ($r['vat'] * $r['net'] * $r['q']);
            $totals['brut'] = $totals['brut'] + ($r['brut'] * $r['q']);
            $totals['items_count'] = $totals['items_count'] + $r['q'];

        }
        $totals['rows'] = count($this->cart);
        $this->totals = $totals;
        return $this->totals;
    }
    public function make_test(){
        echo __METHOD__;
    }

}
