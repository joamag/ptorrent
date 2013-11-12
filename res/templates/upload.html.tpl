{extends file="partials/layout.html.tpl"}
{block name=title}Upload{/block}
{block name=name}Upload{/block}
{block name=body}
<form action="upload.php" method="post" enctype="multipart/form-data">
    <label for="file">Filename:</label>
    <input type="file" name="file" id="file" />
    <br />
    <input type="submit" name="submit" value="Submit" />
</form>
{/block}
