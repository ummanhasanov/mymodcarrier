<?php

class MyModCarrier extends CarrierModule {

    public function __construct() {
        $this->name = 'mymodcarrier';
        $this->tab = 'shipping_logistics';
        $this->version = '0.1';
        $this->author = 'Umman Hasanov';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('MyMod Carrier');
        $this->description = $this->l('A simple carrier module');
    }

    public function getHookController($hook_name) {
        require_once(dirname(__FILE__) . '/controllers/hook/' . $hook_name . '.php');
        $controller_name = $this->name . $hook_name . 'Controller';
        $controller = new $controller_name($this, __FILE__, $this->_path);
        return $controller;
    }

    public function getContent() {
        $controller = $this->getHookController('getContent');
        return $controller->run();
    }

    public function getOrderShippingCost($params, $shipping_cost) {
        return 23;
    }

    public function getOrderShippingCostExternal($params) {
        return false;
    }

}
