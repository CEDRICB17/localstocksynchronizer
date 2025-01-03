<?php
/**
 * 2010-2019 PrestaAlba
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement that you can get at:
 * http://addons-modules.com/store/en/content/3-terms-and-conditions-of-use
 *
 *  @author    PrestaAlba <ventas@prestaalba.com>
 *  @copyright 2010-2019 Addons-Modules.com - PrestaAlba
 *  @license   http://addons-modules.com/store/en/content/3-terms-and-conditions-of-use
 */

class LSSModuleFormSettings extends LSSModuleForm
{

    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'settings';
        $this->submit_action = 'submit'.Tools::ucfirst($this->menu_active).'Form';
    }

    public function getFormFields()
    {
        return array(
            array('form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                        'icon' => 'icon-cog',
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'name' => $this->p.'FRONT',
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
                            'name' => $this->p.'REF_LENGHT',
                            'label' => $this->l('Characters in products'),
                            'desc' => $this->l('Set to 0 to use entire number length for comparison.')
                        ),
                        array(
                            'type' => 'select',
                            'name' => $this->p.'FIELD',
                            'label' => $this->l('Of field'),
                            'options' => array(
                                'query' => $this->matchFieldsList(),
                                'id' => 'id',
                                'name' => 'label'
                            )
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                )
            )
        );
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = array(
            ($name = $this->p.'FIELD') => trim(Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p.'REF_LENGHT') => abs((int)Tools::getValue($name, Configuration::get($name))),
            ($name = $this->p.'FRONT') => abs((int)Tools::getValue($name, Configuration::get($name))),
        );

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues(true);
        $panel = $this->l('Settings').' > ';

        if ($this->submit_action == 'submit'.Tools::ucfirst($this->menu_active).'Form') {
            if (!in_array($val[$this->p.'FIELD'], $this->matchFieldsList(true))) {
                return $panel.$this->l('Of field').': '.$this->l('Must select one from the list.');
            }
        }

        return false;
    }

    private function matchFieldsList($values_only = false)
    {
        $list = array(
            array(
                'id' => 'reference',
                'label' => $this->l('Reference'),
            ),
            array(
                'id' => 'supplier_reference',
                'label' => $this->l('Supplier reference'),
            ),
            array(
                'id' => 'upc',
                'label' => $this->l('UPC'),
            ),
            array(
                'id' => 'ean13',
                'label' => $this->l('EAN-13'),
            ),
        );

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $list = array_merge($list, array(
                array(
                    'id' => 'isbn',
                    'label' => $this->l('ISBN'),
                ),
            ));
        }

        if ($values_only === true) {
            $list = array_column($list, 'id');
        }

        return $list;
    }
}
