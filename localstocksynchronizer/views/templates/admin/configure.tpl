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

<div id="configure_content" class="clearfix">
    <div class="col-lg-2 configure-menu">
        {foreach from=$localstocksynchronizer.menu.items item=group}
            <div class="list-group">
                {foreach from=$group item=item key=key}
                    <a
                        href="{$localstocksynchronizer.menu.link|escape:'htmlall':'UTF-8'}&menu_active={$key|escape:'htmlall':'UTF-8'}"
                        class="list-group-item{if $key == $localstocksynchronizer.menu.active} active{/if}"
                    >
                        <i class="{$item.icon|escape:'htmlall':'UTF-8'}"></i>
                        <span class="title">{$item.title|escape:'htmlall':'UTF-8'}</span>
                        {if $key == 'dashboard' && $localstocksynchronizer.new_version}
                            <span class="badge badge-warning badge-pill">{l s='update' mod='localstocksynchronizer'}</span>
                        {/if}
                    </a>
                {/foreach}
            </div>
        {/foreach}
        <div class="list-group">
            <span class="list-group-item">
                <i class="icon-info"></i>
                <span class="title">{l s='Version' mod='localstocksynchronizer'} {$localstocksynchronizer.version|escape:'htmlall':'UTF-8'}</span>
            </span>
        </div>
    </div>

    <div class="col-lg-10 configure-form">
        {$localstocksynchronizer.form nofilter}
    </div>
</div>
