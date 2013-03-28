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
                    <a href="index.php" class="active">home</a>
                    //
                    <a href="files.php">files</a>
                    //
                    <a href="about.php">about</a>
                </div>
            {/block}
    	</div>
    	<div id="content">{block name=body}{/block}</div>
    	{include file="partials/footer.html.tpl"}
    </body>
</html>
