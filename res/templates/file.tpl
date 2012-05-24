<html>
	<head>
		<title>File</title>
	</head>
	<body>
		<ul>
            <li>Name -> {$file.name}</li>
			<li>Info Hash -> {$file.info_hash_b64}</li>
            <li>Size -> {$file.size}</li>
            <li>Seeders -> {$file.complete}</li>
            <li>Leachers -> {$file.incomplete}</li>
        </ul>
        <ul>
            {foreach from=$file.peers item=peer}
                <li>Peer ID -> {$peer.peer_id}</li>
                <li>IP -> {$peer.ip}</li>
                <li>Port -> {$peer.port}</li>
                <li>Client -> {$peer.client}</li>
                <li>Version -> {$peer.version}</li>
                <li>Status -> {$peer.status}</li>
                <li>Downloaded -> {$peer.downloaded}</li>
                <li>Uploaded -> {$peer.uploaded}</li>
                <li>Peer ID -> {$peer.tobias}</li>
                <li></li>
            {/foreach}
        </ul>
	</body>
</html>