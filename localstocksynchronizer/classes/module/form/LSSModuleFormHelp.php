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

class LSSModuleFormHelp extends LSSModuleForm
{

    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'help';
        $this->tpl = 'configure-help.tpl';
    }

    public function renderForm()
    {
        $this->module->boSmartyAssign(array(
            'documentation' => array(
                array(
                    'lang' => $this->l('english'),
                    'link' => $this->module->getPathUri().'docs/readme_en.pdf',
                ),
                array(
                    'lang' => $this->l('spanish'),
                    'link' => $this->module->getPathUri().'docs/readme_es.pdf',
                ),
            ),
            'support_link' => LSSTools::getLink('support', $this->module),
            'rate_link' => LSSTools::getLink('rate', $this->module),
        ));

        return parent::renderForm();
    }
}
