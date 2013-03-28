{extends file="partials/layout.tpl"}
{block name=title}Files{/block}
{block name=body}
<ul>
    {foreach from=$files item=file}
        <li>
            <a href="file.php?info_hash={$file.info_hash_b64|escape:'url'}">{$file.name} ({$file.size} bytes)</a>
        </li>
    {/foreach}
</ul>
{/block}
