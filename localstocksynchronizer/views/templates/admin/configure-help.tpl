{**
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
 *}

<div class="help-form">
    <div class="col-lg-4">
        <div class="panel documentation">
            <div class="title">
                <i class="icon icon-book"></i>
                <span>{l s='Documentation' mod='localstocksynchronizer'}</span>
            </div>
            <div class="content">
                <p>
                    {l s='Before starting, it is very important that you read the documentation carefully. The module perfect operation depends on a correct configuration.' mod='localstocksynchronizer'}
                </p>
                <p>
                    <ul>
                    {foreach from=$localstocksynchronizer.documentation item=doc}
                        <li><a href="{$doc.link|escape:'htmlall':'UTF-8'}" target="_blank">{$doc.lang|escape:'htmlall':'UTF-8'}</a></li>
                    {/foreach}
                    </ul>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel support">
            <div class="title">
                <i class="icon icon-support"></i>
                <span>{l s='Support' mod='localstocksynchronizer'}</span>
            </div>
            <div class="content">
                <p>
                    {l s='Do you have a problem? First, check the FAQ section in the documentation. There you can find answers and solutions to the most common issues when using the module.' mod='localstocksynchronizer'}
                </p>
                <p>
                    <a href="{$localstocksynchronizer.support_link|escape:'htmlall':'UTF-8'}" target="_blank">{l s='get support' mod='localstocksynchronizer'}</a>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel rate">
            <div class="title">
                <i class="icon icon-star"></i>
                <span>{l s='Rate Us' mod='localstocksynchronizer'}</span>
            </div>
            <div class="content">
                <p>
                    {l s='For us it is very important your review. If our module and support has been useful, please do not hesitate to leave us your rating and a comment.' mod='localstocksynchronizer'}
                </p>
                <p>
                    <a href="{$localstocksynchronizer.rate_link|escape:'htmlall':'UTF-8'}" target="_blank">{l s='help us to keep improving' mod='localstocksynchronizer'}</a>
                </p>
            </div>
        </div>
    </div>
</div>
