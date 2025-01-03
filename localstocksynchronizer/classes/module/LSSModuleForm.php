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

class LSSModuleForm
{
    public $menu_active;
    protected $submit_action;
    protected $p;
    protected $tpl;
    protected $module;
    protected $currentIndex;
    private static $default_class = 'LSSModuleFormDashboard';

    public function __construct()
    {
        $this->module = Module::getInstanceByName('localstocksynchronizer');
        $this->context = Context::getContext();
        $this->currentIndex = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name;
        $this->p = 'LSS_';
    }

    final public static function getForm($form_name, $menu = array())
    {
        $class_name = 'LSSModuleForm'.Tools::ucfirst($form_name);

        if (!empty($form_name) && class_exists($class_name)) {
            if ($menu && !in_array($form_name, array_keys(call_user_func_array('array_merge', $menu)))) {
                return new self::$default_class();
            }

            return new $class_name();
        }

        return new self::$default_class();
    }

    public function renderForm()
    {
        $form_fields = $this->getFormFields();

        if ($form_fields) {
            $helper = new HelperForm();
            $helper->tpl_vars = array(
                'fields_value' => $this->getFormValues(),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            );
            $helper->show_toolbar = false;
            $helper->module = $this->module;
            $helper->default_form_language = $this->context->language->id;
            $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
            $helper->submit_action = $this->submit_action;
            $helper->currentIndex = $this->currentIndex.'&menu_active='.$this->menu_active;
            $helper->token = Tools::getAdminTokenLite('AdminModules');

            return $helper->generateForm($this->getFormFields());
        } else {
            $file = $this->module->getLocalPath().'views/templates/admin/'.$this->tpl;

            if (@is_file($file)) {
                return $this->context->smarty->fetch($file);
            }
        }
    }

    public function getFormFields()
    {
        return array();
    }

    public function isSubmitForm()
    {
        return Tools::isSubmit($this->submit_action);
    }

    public function validateForm()
    {
        return false;
    }

    public function getFormValues($for_save = false)
    {
        return array();
    }

    public function processForm()
    {
        $val = $this->getFormValues(true);

        foreach ($val as $k => $v) {
            Configuration::updateValue($k, $v);
        }

        return $this->l('Configuration updated successfully.');
    }

    protected function l($string)
    {
        return $this->module->l($string, get_class($this));
    }
}
