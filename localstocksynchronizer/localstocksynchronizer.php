<?php
/**
 * 2010-2018 PrestaAlba
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement that you can get at:
 * http://addons-modules.com/store/en/content/3-terms-and-conditions-of-use
 *
 *  @author    PrestaAlba <ventas@prestaalba.com>
 *  @copyright 2010-2018 Addons-Modules.com - PrestaAlba
 *  @license   http://addons-modules.com/store/en/content/3-terms-and-conditions-of-use
 */

if (!defined('_PS_VERSION_'))
    exit;

class LocalStockSynchronizer extends Module
{

    public function __construct()
    {
        $this->name = 'localstocksynchronizer';
        $this->author = 'PrestaAlba';
        $this->version = '1.0.2';
        $this->tab = 'others';
        $this->module_key = 'eb20a9eb8774869ee526805593f9a8c3';
        $this->author_address = '0xB0c3CF0a6a0ffFeB362225a66A1DF500CE784Ea7';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Local Stock Synchronizer');
        $this->description = $this->l('Maintain local stock synchronized between products of similar reference, EAN-13 or UPC code.');
    }

    public function install()
    {
        return (parent::install() &&
                Configuration::updateGlobalValue('LSS_REF_LENGHT', '0') &&
                Configuration::updateGlobalValue('LSS_FRONT', '1') &&
                Configuration::updateGlobalValue('LSS_FIELD', 'reference') &&
                $this->registerHook('actionObjectStockUpdateAfter') &&
                $this->registerHook('actionObjectStockAddAfter') &&
                $this->registerHook('actionUpdateQuantity'));
    }

    public function uninstall()
    {
        return (parent::uninstall() && Configuration::deleteByName('LSS_REF_LENGHT') && Configuration::deleteByName('LSS_FRONT'));
    }

    public function getContent()
    {
        $html = '';
        if (Tools::isSubmit('submitSaveLocalStock'))
            if (Validate::isUnsignedInt(Tools::getValue('LSS_REF_LENGHT')))
            {
                Configuration::updateGlobalValue('LSS_REF_LENGHT', Tools::getValue('LSS_REF_LENGHT'));
                Configuration::updateGlobalValue('LSS_FRONT', Tools::getValue('LSS_FRONT'));
                Configuration::updateGlobalValue('LSS_FIELD', Tools::getValue('LSS_FIELD'));

                $html = $this->displayConfirmation($this->l('Configuration saved successfully.'));
            }
            else
                $html = $this->displayError($this->l('Characters in products number must be a valid positive integer.'));

        return $html . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSaveLocalStock';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array('form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cog',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'name' => 'LSS_FRONT',
                        'label' => $this->l('Compare the'),
                        'options' => array(
                            'query' => array(
                                array('id' => '1', 'name' => $this->l('First')),
                                array('id' => '0', 'name' => $this->l('Last'))
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'LSS_REF_LENGHT',
                        'label' => $this->l('Characters in products'),
                        'desc' => $this->l('Set to 0 to use entire number length for comparison.')
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'LSS_FIELD',
                        'label' => $this->l('Of field'),
                        'options' => array(
                            'query' => array(
                                array('id' => 'reference', 'name' => $this->l('Reference')),
                                array('id' => 'upc', 'name' => $this->l('UPC')),
                                array('id' => 'ean13', 'name' => $this->l('EAN-13'))
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'LSS_REF_LENGHT' => Configuration::getGlobalValue('LSS_REF_LENGHT'),
            'LSS_FRONT' => Configuration::getGlobalValue('LSS_FRONT'),
            'LSS_FIELD' => Configuration::getGlobalValue('LSS_FIELD'),
        );
    }

    public function hookActionUpdateQuantity($params)
    {
        //la primera llamada es para el total de cantidades del producto
        //la segunda llamada para actualizar la combinacion especifica
        $id_product = $params['id_product'];
        $id_product_attribute = $params['id_product_attribute'];
        $cantidad = $params['quantity'];
        $stock_avanzado = isset($params['stock']) ? true : false;
        $longitud = Configuration::getGlobalValue('LSS_REF_LENGHT') ? (int) Configuration::getGlobalValue('LSS_REF_LENGHT') : false;
        $comparar_inicio = (bool) Configuration::getGlobalValue('LSS_FRONT');
        $campo = Configuration::getGlobalValue('LSS_FIELD');

        $referencia = Db::getInstance()->getValue('SELECT `' . $campo . '` 
                        FROM `' . _DB_PREFIX_ . 'product_attribute` 
                        WHERE `id_product` = ' . $id_product
                . ' AND `id_product_attribute` = ' . $id_product_attribute);
        if (!$referencia)
            $referencia = Db::getInstance()->getValue('SELECT `' . $campo . '` 
                        FROM `' . _DB_PREFIX_ . 'product` 
                        WHERE `id_product` = ' . $id_product);
        if ($referencia)
        {
            if ($comparar_inicio)
                $referencia_comparar = Tools::substr($referencia, 0, $longitud) . '%';
            else
                $referencia_comparar = '%' . Tools::substr($referencia, -(int) $longitud);

            //actualizacion de combinaciones
            $sql = 'SELECT `id_product`, `id_product_attribute` 
            FROM ' . _DB_PREFIX_ . 'product_attribute 
            WHERE `' . $campo . '` LIKE "' . $referencia_comparar . '"';

            $id_combinaciones_similares = Db::getInstance()->executeS($sql);
            foreach ($id_combinaciones_similares as $prod)
            {
                if ($stock_avanzado)
                    $this->setQuantityStock((int) $prod['id_product'], (int) $prod['id_product_attribute'], $params['stock']);
                $this->setQuantityStockAvailable((int) $prod['id_product'], (int) $prod['id_product_attribute'], $cantidad);
            }
            //actualizacion de productos
            $sql = 'SELECT `id_product`
            FROM ' . _DB_PREFIX_ . 'product
            WHERE `' . $campo . '` LIKE "' . $referencia_comparar . '"'
                    . ($stock_avanzado ? '' : ' AND `id_product` != ' . $id_product)
                    . ' AND `id_product` NOT IN ('
                    . 'SELECT DISTINCT `id_product` FROM `' . _DB_PREFIX_ . 'stock_available` WHERE `id_product_attribute` > 0)';

            $id_productos_similares = Db::getInstance()->executeS($sql);
            foreach ($id_productos_similares as $prod)
            {
                if ($stock_avanzado)
                    $this->setQuantityStock((int) $prod['id_product'], (int) $prod['id_product_attribute'], $params['stock']);
                $this->setQuantityStockAvailable((int) $prod['id_product'], 0, $cantidad);
            }
        }
    }

    private function setQuantityStockAvailable($id_product, $id_product_attribute, $quantity, $id_shop = null)
    {
        if (!Validate::isUnsignedId($id_product))
            return false;

        $context = Context::getContext();

        if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP)
            $id_shop = (int) $context->shop->id;

        $id_stock_available = (int) StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);
        if ($id_stock_available)
        {
            Db::getInstance()->update('stock_available', array('quantity' => (int) $quantity), '`id_stock_available`=' . $id_stock_available);
            $this->postSaveChangeQuantity($id_product, $id_product_attribute, $id_shop);
        }
        else
        {
            $valores = array(
                'out_of_stock' => 0,
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
                'quantity' => (int) $quantity,
            );

            if ($id_shop === null)
                $shop_group = Shop::getContextShopGroup();
            else
                $shop_group = new ShopGroup((int) Shop::getGroupFromShop((int) $id_shop));

            if ($shop_group->share_stock)
            {
                $valores['id_shop'] = 0;
                $valores['id_shop_group'] = (int) $shop_group->id;
            }
            else
            {
                $valores['id_shop'] = (int) $id_shop;
                $valores['id_shop_group'] = 0;
            }

            Db::getInstance()->insert('stock_available', $valores);
            $this->postSaveChangeQuantity($id_product, $id_product_attribute, $id_shop);
        }
    }

    private function postSaveChangeQuantity($id_product, $id_product_attribute, $id_shop = null)
    {
        if ($id_product_attribute > 0)
        {
            $id_shop = (Shop::getContext() != Shop::CONTEXT_GROUP && $id_shop ? $id_shop : null);

            $total_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(quantity)
			FROM ' . _DB_PREFIX_ . 'stock_available
			WHERE id_product = ' . (int) $id_product
                            . ' AND `id_product_attribute` > 0 ' .
                            StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
            $this->setQuantityStockAvailable($id_product, 0, $total_quantity, $id_shop);
        }
    }

    private function setQuantityStock($id_product, $id_product_attribute, $stock)
    {
        if (!Validate::isUnsignedId($id_product) || !Validate::isLoadedObject($stock))
            return false;

        $stock_collection = new PrestaShopCollection('Stock');
        $stock_collection->where('id_product', '=', $id_product);
        $stock_collection->where('id_product_attribute', '=', $id_product_attribute);
        $stock_collection->where('id_warehouse', '=', $stock->id_warehouse);

        if (count($stock_collection) > 0)
        {
            $stock_cambiar = $stock_collection->current();

            $stock_params = array(
                'physical_quantity' => $stock->physical_quantity,
                'usable_quantity' => $stock->usable_quantity,
            );

            Db::getInstance()->update('stock', $stock_params, '`id_stock`=' . $stock_cambiar->id);
        }
        else
        {
            $stock_params = array(
                'id_product_attribute' => $id_product_attribute,
                'id_product' => $id_product,
                'physical_quantity' => $stock->physical_quantity,
                'price_te' => $stock->price_te,
                'usable_quantity' => $stock->usable_quantity,
                'id_warehouse' => $stock->id_warehouse
            );
            if ($id_product_attribute > 0)
            {
                $datos_adicionales = Db::getInstance()->getRow('SELECT reference, ean13, upc '
                        . 'FROM ' . _DB_PREFIX_ . 'product_attribute '
                        . 'WHERE id_product = ' . $id_product . ' '
                        . ' AND id_product_attribute = ' . $id_product_attribute);
                if ($datos_adicionales)
                {
                    $stock_params['reference'] = $datos_adicionales['reference'];
                    $stock_params['ean13'] = $datos_adicionales['ean13'];
                    $stock_params['upc'] = $datos_adicionales['upc'];
                }
            }
            else
            {
                $datos_adicionales = Db::getInstance()->getRow('SELECT reference, ean13, upc '
                        . 'FROM ' . _DB_PREFIX_ . 'product '
                        . 'WHERE id_product = ' . $id_product);
                if ($datos_adicionales)
                {
                    $stock_params['reference'] = $datos_adicionales['reference'];
                    $stock_params['ean13'] = $datos_adicionales['ean13'];
                    $stock_params['upc'] = $datos_adicionales['upc'];
                }
            }

            Db::getInstance()->insert('stock', $stock_params);
        }

        $id_wpl = (int) WarehouseProductLocation::getIdByProductAndWarehouse($id_product, $id_product_attribute, $stock->id_warehouse);
        if (!$id_wpl)
        {
            $wpl = new WarehouseProductLocation();
            $wpl->id_product = (int) $id_product;
            $wpl->id_product_attribute = (int) $id_product_attribute;
            $wpl->id_warehouse = (int) $stock->id_warehouse;
            $wpl->save();
        }
    }

    public function hookActionObjectStockUpdateAfter($params)
    {
        $stock = $params['object'];
        $this->hookActionUpdateQuantity(array(
            'id_product' => $stock->id_product,
            'id_product_attribute' => $stock->id_product_attribute,
            'quantity' => $stock->usable_quantity,
            'stock' => $stock
        ));
    }

    public function hookActionObjectStockAddAfter($params)
    {
        $this->hookActionObjectStockUpdateAfter($params);
    }

}
