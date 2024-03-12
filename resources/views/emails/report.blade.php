<!DOCTYPE html>
<html>

<head>
    <title>Käyttäjä ilmiannettu</title>
</head>

<body>
    <div>
        <p>
            Käyttäjä {{ $reporter }} on ilmiantanut käyttäjän {{ $reported }} seuraavalla
            perusteella:

            {{ $content }}
        </p>
    </div>
</body>

</html>