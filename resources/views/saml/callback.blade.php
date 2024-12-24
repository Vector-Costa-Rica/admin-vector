<!DOCTYPE html>
<html>
<head>
    <title>Redireccionando...</title>
</head>
<body>
<script>
    @if(isset($error))
    console.error('Error:', @json($error));
    @endif

        window.location.href = @json($redirectUrl);
</script>
<noscript>
    Por favor espere mientras es redireccionado...
    <meta http-equiv="refresh" content="0;url={{ $redirectUrl }}">
</noscript>
</body>
</html>
