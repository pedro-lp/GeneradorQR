<?php
require('phpqrcode/qrlib.php');
#Comprobar si la variable está definida
if (isset($_POST['enviar'])) {

}
$dir='temp/';
if(!file_exists($dir))
    mkdir($dir);
$fileName = $dir.date('d-M-Y_H:i:s').".png";
$tam=15;
$level ="H";
$frameSize=1;
/*
- Texto: 'Mi primer QR'
- URL: 'https://fb.me/@PesEdit1'
- Télefono: 'tel:(747)124-9023
- SMS: 'smsto:(747)104-1206:Cuerpo de sms, este es un mensaje de texto de prueba, desde un QR'
- Email: 'mailto:piter_pacheco@hotmail.com?subject=CorreodePrueba&body=ContenidoDelCorreoQueQuiereEnviar'
- VCard: 'BEGIN:VCARD'.'\n»;
'FN:Codigos de Programacion'.»\n»;
'TEL;WORK;VOICE:(049)012-345-678'.»\n»;
'END:VCARD
- WIFI: 'WIFI:S:MISSID;T:WPA;P:PASSWORD;H:false;'
*/
$contenido='https://fb.me/@PesEdit1';
QRcode::png($contenido, $fileName, $level, $tam, $frameSize);

echo "<img src=".$fileName." />";
?>
<html lang="es">
<!-- cabecera de la pagina web -->
<head>
    <!-- importacion de bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- titulo de la pagina-->
    <title>Login con sesiones</title>
    <nav class="navbar navbar-light bg-info justify-content-between">
        <a class="navbar-brand text-white" href="?">Gestionar Estacionamiento</a>
    </nav>
</head>

<!-- cuerpo de la pagina-->
<body style="background: linear-gradient(180deg, #C6FCFC, #A7FC97);">
    <div class="container p-4">
        <center>
            <h2>Inicio de Sesión</h2>
            <!-- imagen -->
            <div>
                <img src="img/logo.png" width="100" class="d-inline-block align-top" alt="" loading="lazy">
            </div> <br>
            <div class="p-3 mb-2 bg-secondary text-white w-50">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="form-group">
                        <label for="usuario">Usuario</label><br>
                        <input class="form-control w-75" type="text" name="usuario" id="usuario" placeholder="Ingresar Usuario">
                    </div>
                    <div class="form-group">
                        <label for="clave">Contraseña</label><br>
                        <input class="form-control w-75" type="password" name="clave" id="clave" placeholder="Ingresar Contraseña">
                    </div>
                        <!-- link para registrarse -->
                    <a class="text-white" href='registrarse.php'>
                        <h5>¿no tienes cuenta? Registrate</h5>
                    </a><br>
                        <!-- boton enviar -->
                    <div class="form-group">
                        <button class="btn btn-primary" name="enviar" id="enviar" type="submit">Iniciar Sesion</button>
                    </div>
                </form>
            </div>
        </center>
    </div>
</body>

</html>