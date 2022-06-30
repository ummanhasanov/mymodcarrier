<?php

class MyModCarrier extends CarrierModule {

    public $id_carrier;

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

    public function install() {
        if (!parent::install()) {
            return false;
        }
        if (!$this->installCarriers()) {
            return false;
        }
        return true;
    }

    public function installCarriers() {
        $id_lang_default = Language::getIsoById(Configuration::get('PS_LANG_DEFAULT'));
        $carrier_list = [
            'MYMOD_CA_CLDE' => 'Classic delivery',
            'MYMOD_CA_REPO' => 'Relay Point'
        ];
        foreach ($carrier_list as $carrier_key => $carrier_name) {
            if (Configuration::get($carrier_key) < 1) {

                // Create new carrier
                $carrier = new Carrier();
                $carrier->name = $carrier_name;
                $carrier->id_tax_rules_group = 0;
                $carrier->active = 1;
                $carrier->deleted = 0;
                foreach (Language::getLanguages(true) as $language) {
                    $carrier->delay[(int) $language['id_lang']] = 'Delay' . $carrier_name;
                }
                $carrier->shipping_handling = false;
                $carrier->range_behavior = 0;
                $carrier->is_module = true;
                $carrier->shipping_external = true;
                $carrier->external_module_name = $this->name;
                $carrier->need_range = true;
                if (!$carrier->add()) {
                    return false;
                }
                // Associate carrier to all groups
                $groups = Group::getGroups(true);
                foreach ($groups as $group) {
                    DB::getInstance()->insert('carrier_group', array('id_carrier' =>
                        (int) $carrier->id, 'id_group' => (int) $group['id_group']));
                }
                // Create price range
                $rangePrice = new RangePrice();
                $rangePrice->id_carrier = $carrier->id;
                $rangePrice->delimiter1 = '0';
                $rangePrice->delimiter2 = '10000';
                $rangePrice->add();

                // Create price range
                $rangeWeight = new RangeWeight();
                $rangeWeight->id_carrier = $carrier->id;
                $rangeWeight->delimiter1 = '0';
                $rangeWeight->delimiter2 = '10000';
                $rangeWeight->add();

                //Associate carrier to all zones
                $zones = Zone::getZones(true);
                foreach ($zones as $zone) {
                    Db::getInstance()->insert('carrier_zone', array('id_carrier' =>
                        (int) $carrier->id, 'id_zone' => (int) $zone['id_zone']));
                    Db::getInstance()->insert('delivery', array('id_carrier' =>
                        (int) $carrier->id, 'id_range_price' => (int) $rangePrice->id,
                        'id_range_weight' => NULL, 'id_zone' => (int) $zone['id_zone'], 'price' => '0'));
                    Db::getInstance()->insert('delivery', array('id_carrier' =>
                        (int) $carrier->id, 'id_range_price' => NULL, 'id_range_weight' =>
                        (int) $rangeWeight->id, 'id_zone' => (int) $zone['id_zone'], 'price' => '0'));
                }
                // Copy the carrier logo
                copy(dirname(__FILE__) . '/views/img/' . $carrier_key . '.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg');

                //Save the Carrier ID in the Configuration table
                Configuration::updateValue($carrier_key, $carrier->id);
            }
        }
        return true;
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
        $contoller = $this->getHookController('getOrderShippingCost');
        return $contoller->run($params, $shipping_cost);
    }

    public function getOrderShippingCostExternal($params) {
        return $this->getOrderShippingCost($params, 0);
    }

}
