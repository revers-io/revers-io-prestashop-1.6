{**
* Copyright (c) 2019 Revers.io
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is furnished
* to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @author revers.io
* @copyright Copyright (c) permanent, Revers.io
* @license   Revers.io
* @see       /LICENSE
*}

{extends file="helpers/options/options.tpl"}

{block name="input" append}
    {if $field['type'] == 'progress'}
        <div class="col-lg-9 {if isset($field['class'])}{$field['class']|escape:'htmlall':'UTF-8'}{/if}">
            <div id="order-import-information"></div>
        </div>
    {/if}
    {if $field['type'] == 'orders_status'}
        <div class="col-lg-5 {if isset($field['class'])}{$field['class']|escape:'htmlall':'UTF-8'}{/if}">
            {include file="../../../partials/orders-statuses.tpl"}
        </div>
    {/if}
    {if $field['type'] == 'order_date_from_to'}
        <div class="col-lg-5 {if isset($field['class'])}{$field['class']|escape:'htmlall':'UTF-8'}{/if}">
            {include file="../../../partials/orders-date.tpl"}
        </div>
    {/if}
    {if $field['type'] == 'desc'}
        <div class="col-lg-5 {if isset($field['class'])}{$field['class']|escape:'htmlall':'UTF-8'}{/if}">
            {include file="../../../partials/orders-initial-import-desc.tpl"}
        </div>
    {/if}
{/block}
