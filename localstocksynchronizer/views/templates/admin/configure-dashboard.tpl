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

<div class="dashboard-form">
    <div class="col-lg-12{if !$localstocksynchronizer.new_version} hidden{/if}">
        <div class="panel module-update">
            <p>
                <strong>{l s='A new version %s update is now available!' mod='localstocksynchronizer' sprintf=$localstocksynchronizer.new_version}</strong>
                <a href="{$localstocksynchronizer.module_link|escape:'htmlall':'UTF-8'}" class="btn btn-warning" target="_bank">
                    {l s='Download now' mod='localstocksynchronizer'}
                </a>
            </p>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel module-info">
            <p class="logo">
                <img src="{$localstocksynchronizer._path|escape:'htmlall':'UTF-8'}logo.png" />
            </p>
            <p class="title">
                {$localstocksynchronizer.displayName|escape:'htmlall':'UTF-8'}
            </p>
            <p class="description">
                {$localstocksynchronizer.description|escape:'htmlall':'UTF-8'}
            </p>
            <p class="reference">
                <a href="{$localstocksynchronizer.module_link|escape:'htmlall':'UTF-8'}" target="_bank">{l s='more info' mod='localstocksynchronizer'}</a>
            </p>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel partner-info">
            <p class="title">
                {l s='We are PrestaShop Superhero!' mod='localstocksynchronizer'}
            </p>
            <p class="logo">
                <a class="rolige-logo" href="{$localstocksynchronizer.author_link|escape:'htmlall':'UTF-8'}" target="_bank">
                    <img class="img-responsive" src="{$localstocksynchronizer._path|escape:'htmlall':'UTF-8'}logo.png" />
                </a>
                <a class="partner-logo" href="{$localstocksynchronizer.author_link|escape:'htmlall':'UTF-8'}" target="_bank">
                    <i/>
                </a>
            </p>
        </div>
    </div>
    {if count($localstocksynchronizer.products_marketing)}
    <div class="col-lg-12">
        <div class="panel products-marketing">
            <div class="title">
                {l s='Other of our partners excellent and certified modules!' mod='localstocksynchronizer'}
            </div>
            <div class="products-marketing-list">
            {foreach $localstocksynchronizer.products_marketing as $prod}
                <div>
                    <div class="image">
                        <a href="{$prod.prod_url|escape:'htmlall':'UTF-8'}" target="_blank">
                            <img class="img-responsive" src="{$prod.img_url|escape:'htmlall':'UTF-8'}" alt="{$prod.name|escape:'htmlall':'UTF-8'}" />
                        </a>
                    </div>
                    <div class="info">
                        <div class="name">
                            <a href="{$prod.prod_url|escape:'htmlall':'UTF-8'}" target="_blank">
                                {$prod.name|escape:'htmlall':'UTF-8'}
                            </a>
                        </div>
                        <div class="price">
                            {if $localstocksynchronizer.source == 'rolige'}
                                {if $prod.price.base != $prod.special_price.base}
                                    <span class="discount-percentage">
                                        -{(100 - ($prod.special_price.base / $prod.price.base) * 100)|string_format:'%d'}%
                                    </span>
                                    <span class="base-price">{$prod.price.display|escape:'htmlall':'UTF-8'}</span>
                                    <span class="special-price">{$prod.special_price.display|escape:'htmlall':'UTF-8'}</span>
                                {else}
                                    <span class="special-price">{$prod.price.display|escape:'htmlall':'UTF-8'}</span>
                                {/if}
                            {else}
                                <span class="special-price">{$prod.price.display|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
    {/if}
</div>
