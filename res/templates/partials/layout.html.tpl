<html>
    <head>
        {include file="partials/includes.html.tpl"}
        <title>pTorrent / {block name=title}{/block}</title>
    </head>
    <body>
        <div id="header">
            {block name=header}
                <h1>{block name=name}{/block}</h1>
                <div class="links">
                    {if $link eq "home"}
                        <a href="index.php" class="active">home</a>
                    {else}
                        <a href="index.php">home</a>
                    {/if}
                    //
                    {if $link eq "files"}
                        <a href="files.php" class="active">files</a>
                    {else}
                        <a href="files.php">files</a>
                    {/if}
                    //
                    {if $link eq "about"}
                        <a href="about.php" class="active">about</a>
                    {else}
                        <a href="about.php">about</a>
                    {/if}
                </div>
            {/block}
        </div>
        <div id="content">{block name=body}{/block}</div>
        {include file="partials/footer.html.tpl"}
    </body>
</html>
