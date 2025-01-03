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

class LSSTools
{

    public static function getLink($type, $module = null)
    {
        switch ($type) {
            case 'author':
                return $module->addons_author_link;
            case 'module':
                return 'https://addons.prestashop.com/product.php?id_product='.$module->addons_module_id;
            case 'support':
                return 'https://addons.prestashop.com/contact-form.php?id_product='.$module->addons_module_id;
            case 'rate':
                return 'https://addons.prestashop.com/ratings.php';
        }

        return false;
    }

    public static function getProductsMarketing($module)
    {
        $request_json = false;
        $source = 'addons';
        $config = 'RG_MARKETING_'.Tools::strtoupper($source).'_REQUEST';
        $request = Configuration::getGlobalValue($config);

        if (!$request ||
                (($request_json = Tools::jsonDecode($request, true)) && (int)$request_json['next_request'] < time())
        ) {
            $ch = curl_init('https://www.rolige.com/modules/rg_webservice/api/productsmarketing');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Tools::jsonEncode(array(
                        'key' => '764438a9bd64fdae8e5b1065d4741eab',
                        'params' => array(
                            'module' => $module->name,
                            'domain' => Tools::getServerName(),
                            'source' => $source,
                            'country_iso_code' => Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT')),
                            'currency_iso_code' => Currency::getCurrencyInstance((int)Configuration::get('PS_CURRENCY_DEFAULT'))->iso_code,
                            'lang_iso_code' => Language::getIsoById((int)Context::getContext()->language->id),
                        )
            )));

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response && ($request_json = Tools::jsonDecode($response, true)) && count($request_json['products'])) {
                Configuration::updateGlobalValue($config, $response);
            }
        }

        return $request_json ? $request_json['products'] : array();
    }
}
