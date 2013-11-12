{extends file="partials/layout.html.tpl"}
{block name=title}Files{/block}
{block name=name}Files{/block}
{block name=body}
<ul>
    {foreach from=$files item=file}
        <li>
            <a href="file.php?info_hash={$file.info_hash_b64|escape:'url'}">{$file.name} ({$file.size} bytes)</a>
        </li>
    {/foreach}
</ul>
{/block}
