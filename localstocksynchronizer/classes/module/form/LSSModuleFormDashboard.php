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

class LSSModuleFormDashboard extends LSSModuleForm
{

    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'dashboard';
        $this->tpl = 'configure-dashboard.tpl';
    }

    public function renderForm()
    {
        $source = 'addons';
        $this->module->boSmartyAssign(array(
            'displayName' => $this->module->displayName,
            'description' => $this->module->description,
            'author' => $this->module->author,
            'author_link' => LSSTools::getLink('author', $this->module),
            'module_link' => LSSTools::getLink('module', $this->module),
            'partner_link' => LSSTools::getLink('partner'),
            'source' => $source,
            'products_marketing' => LSSTools::getProductsMarketing($this->module, $source),
        ));

        return parent::renderForm();
    }
}
