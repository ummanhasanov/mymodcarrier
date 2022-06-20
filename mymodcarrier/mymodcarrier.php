<?php

class MyModCarrier extends CarrierModule {

    public function __construct() {
        $this->name = 'mymodcarrier';
        $this->tab = 'shipping_logistics';
        $this->version = '0.1';
        $this->author = 'Umman Hasanov';
        $this->bootstrap = true;
        parent::__constract();
        $this->displayName = $this->l('MyMod Carrier');
        $this->description = $this->l('A simple carrier module');
    }

    public function getOrderShippingCost($params, $shipping_cost) {
        return false;
    }

    public function getOrderShippingCostExternal($params) {
        return false;
    }

}





