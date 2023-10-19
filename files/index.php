<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Titre de la page</title>
<link rel="stylesheet" href="custom.css">
</head>
<body>
    <div class="container">
        <h1 style="margin-bottom: 60px;">Formulaire d'import des événements BassFactory</h1>
        <form method="post" action="script.php">
            <label for="wixSiteId">Wix Site Id</label><br>
            <input type="text" id="wixSiteId" name="wixSiteId" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX"><br>
            <label for="wixToken">Wix Token</label><br>
            <input type="text" id="wixToken" name="wixToken" placeholder="IST.eyJraWQiOi............."><br>
            <label for="events">URL d'événements Facebook </label>
            <textarea id="events" name="events" rows="20" cols="80" placeholder="Copiez vos URL d'événements Facebook au format : https://www.facebook.com/events/710815127452973/  Un par ligne! "></textarea>
            <input type="submit" value="Envoyer !">
        </form>
    </div>
</body>
</html>