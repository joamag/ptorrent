<html>
	<head>
		<title>Files</title>
	</head>
	<body>
		<ul>
			{foreach from=$files item=file}
				<li>
                    <a href="file.php?info_hash={$file.info_hash_b64}">{$file.name} ({$file.size} bytes)</a>
                </li>
			{/foreach}
        </ul>
	</body>
</html>