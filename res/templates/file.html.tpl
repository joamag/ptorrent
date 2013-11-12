{extends file="partials/layout.html.tpl"}
{block name=title}File{/block}
{block name=name}File{/block}
{block name=body}
<div class="quote">{$file.name}</div>
<div class="separator-horizontal"></div>
<table>
    <tbody>
        <tr>
            <td class="right label" width="50%">info hash</td>
            <td class="left value" width="50%">{$file.info_hash_b64}</td>
        </tr>
        <tr>
            <td class="right label" width="50%">size</td>
            <td class="left value" width="50%">{$file.size}</td>
        </tr>
        <tr>
            <td class="right label" width="50%">seeders</td>
            <td class="left value" width="50%">{$file.complete}</td>
        </tr>
        <tr>
            <td class="right label" width="50%">leachers</td>
            <td class="left value" width="50%">{$file.incomplete}</td>
        </tr>
    </tbody>
</table>
{foreach from=$file.peers item=peer}
    <div class="separator-horizontal"></div>
    <table>
        <tbody>
            <tr>
                <td class="right label" width="50%">peer id</td>
                <td class="left value" width="50%">{$peer.peer_id}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">ip</td>
                <td class="left value" width="50%">{$peer.ip}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">port</td>
                <td class="left value" width="50%">{$peer.port}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">client</td>
                <td class="left value" width="50%">{$peer.client}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">version</td>
                <td class="left value" width="50%">{$peer.version}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">status</td>
                <td class="left value" width="50%">{$peer.status}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">downloaded</td>
                <td class="left value" width="50%">{$peer.downloaded}</td>
            </tr>
            <tr>
                <td class="right label" width="50%">uploaded</td>
                <td class="left value" width="50%">{$peer.uploaded}</td>
            </tr>
        </tbody>
    </table>
{/foreach}
{/block}
