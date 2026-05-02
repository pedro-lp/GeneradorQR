<?php
require('phpqrcode/qrlib.php');

$qrGenerado = null;
$error = null;

if (isset($_POST['generar'])) {
    $tipo = $_POST['tipo'] ?? '';
    $contenido = '';

    switch ($tipo) {
        case 'texto':
            $contenido = trim($_POST['texto'] ?? '');
            break;
        case 'url':
            $contenido = trim($_POST['url'] ?? '');
            break;
        case 'telefono':
            $contenido = 'tel:' . trim($_POST['telefono'] ?? '');
            break;
        case 'sms':
            $contenido = 'smsto:' . trim($_POST['telefono'] ?? '') . ':' . trim($_POST['mensaje'] ?? '');
            break;
        case 'email':
            $contenido = 'mailto:' . trim($_POST['email'] ?? '')
                . '?subject=' . rawurlencode(trim($_POST['asunto'] ?? ''))
                . '&body='    . rawurlencode(trim($_POST['cuerpo'] ?? ''));
            break;
        case 'vcard':
            $contenido = "BEGIN:VCARD\r\nVERSION:3.0\r\n"
                . "FN:"  . trim($_POST['nombre']      ?? '') . "\r\n"
                . "TEL:" . trim($_POST['tel_vcard']   ?? '') . "\r\n"
                . "EMAIL:". trim($_POST['email_vcard']?? '') . "\r\n"
                . "ORG:" . trim($_POST['empresa']     ?? '') . "\r\n"
                . "END:VCARD";
            break;
        case 'wifi':
            $oculto   = isset($_POST['oculto']) ? 'true' : 'false';
            $contenido = 'WIFI:S:' . trim($_POST['ssid']       ?? '')
                       . ';T:'    . trim($_POST['seguridad']   ?? 'WPA')
                       . ';P:'    . trim($_POST['wifi_pass']   ?? '')
                       . ';H:'   . $oculto . ';';
            break;
    }

    if ($contenido === '') {
        $error = 'Completa los campos antes de generar el QR.';
    } else {
        $dir = 'temp/';
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        $tam = in_array((int)($_POST['tamano'] ?? 10), [5, 10, 15, 20]) ? (int)$_POST['tamano'] : 10;
        $fileName = $dir . date('d-M-Y_H-i-s') . '_' . $tipo . '_' . $tam . '.png';
        QRcode::png($contenido, $fileName, 'H', $tam, 1);
        $qrGenerado = $fileName;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de QR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <style>
        body { background: linear-gradient(135deg, #e0f7fa, #e8f5e9); min-height: 100vh; }
        .card { border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.10); }
        .tipo-btn {
            cursor: pointer; border-radius: 12px; padding: 18px 10px;
            transition: all .15s; text-align: center; border: 2px solid #dee2e6;
            background: #fff;
        }
        .tipo-btn:hover  { border-color: #17a2b8; background: #e0f7fa; }
        .tipo-btn.active { border-color: #17a2b8; background: #17a2b8; color: #fff; }
        .tipo-btn .icono { font-size: 2rem; display: block; }
        .qr-result img   { max-width: 260px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.15); }
        footer { background: #000; color: #fff; padding: 12px 24px; text-align: center; font-size: 14px; }
        .form-section { display: none; }
        .form-section.activo { display: block; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-info">
    <span class="navbar-brand font-weight-bold">&#x25A3; Generador de Códigos QR</span>
</nav>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

            <!-- Selector de tipo -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">¿Qué tipo de QR deseas generar?</h5>
                    <div class="row text-center" id="selector-tipos">
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (!isset($_POST['tipo']) || $_POST['tipo']=='texto')   ? 'active':'' ?>" data-tipo="texto">
                                <span class="icono">📝</span>Texto
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (isset($_POST['tipo']) && $_POST['tipo']=='url')       ? 'active':'' ?>" data-tipo="url">
                                <span class="icono">🔗</span>URL / Link
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (isset($_POST['tipo']) && $_POST['tipo']=='telefono')  ? 'active':'' ?>" data-tipo="telefono">
                                <span class="icono">📞</span>Teléfono
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (isset($_POST['tipo']) && $_POST['tipo']=='sms')       ? 'active':'' ?>" data-tipo="sms">
                                <span class="icono">💬</span>SMS
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (isset($_POST['tipo']) && $_POST['tipo']=='email')     ? 'active':'' ?>" data-tipo="email">
                                <span class="icono">📧</span>Email
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (isset($_POST['tipo']) && $_POST['tipo']=='vcard')     ? 'active':'' ?>" data-tipo="vcard">
                                <span class="icono">👤</span>Contacto
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="tipo-btn <?= (isset($_POST['tipo']) && $_POST['tipo']=='wifi')      ? 'active':'' ?>" data-tipo="wifi">
                                <span class="icono">📶</span>WiFi
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="card mb-4">
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php">
                        <input type="hidden" name="tipo" id="tipo-hidden" value="<?= htmlspecialchars($_POST['tipo'] ?? 'texto') ?>">

                        <!-- TEXTO -->
                        <div class="form-section <?= (!isset($_POST['tipo']) || $_POST['tipo']=='texto') ? 'activo':'' ?>" id="form-texto">
                            <h6 class="text-info mb-3">📝 Texto libre</h6>
                            <div class="form-group">
                                <label>Texto</label>
                                <textarea class="form-control" name="texto" rows="3"
                                    placeholder="Escribe cualquier texto..."><?= htmlspecialchars($_POST['texto'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- URL -->
                        <div class="form-section <?= (isset($_POST['tipo']) && $_POST['tipo']=='url') ? 'activo':'' ?>" id="form-url">
                            <h6 class="text-info mb-3">🔗 URL / Link</h6>
                            <div class="form-group">
                                <label>Dirección web</label>
                                <input class="form-control" type="url" name="url"
                                    placeholder="https://ejemplo.com"
                                    value="<?= htmlspecialchars($_POST['url'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- TELÉFONO -->
                        <div class="form-section <?= (isset($_POST['tipo']) && $_POST['tipo']=='telefono') ? 'activo':'' ?>" id="form-telefono">
                            <h6 class="text-info mb-3">📞 Teléfono</h6>
                            <div class="form-group">
                                <label>Número de teléfono</label>
                                <input class="form-control" type="tel" name="telefono"
                                    placeholder="(747) 123-4567"
                                    value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- SMS -->
                        <div class="form-section <?= (isset($_POST['tipo']) && $_POST['tipo']=='sms') ? 'activo':'' ?>" id="form-sms">
                            <h6 class="text-info mb-3">💬 SMS</h6>
                            <div class="form-group">
                                <label>Número de teléfono</label>
                                <input class="form-control" type="tel" name="telefono"
                                    placeholder="(747) 123-4567"
                                    value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Mensaje</label>
                                <textarea class="form-control" name="mensaje" rows="2"
                                    placeholder="Escribe el mensaje..."><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- EMAIL -->
                        <div class="form-section <?= (isset($_POST['tipo']) && $_POST['tipo']=='email') ? 'activo':'' ?>" id="form-email">
                            <h6 class="text-info mb-3">📧 Email</h6>
                            <div class="form-group">
                                <label>Correo electrónico</label>
                                <input class="form-control" type="email" name="email"
                                    placeholder="ejemplo@correo.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Asunto</label>
                                <input class="form-control" type="text" name="asunto"
                                    placeholder="Asunto del correo"
                                    value="<?= htmlspecialchars($_POST['asunto'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Cuerpo del mensaje</label>
                                <textarea class="form-control" name="cuerpo" rows="2"
                                    placeholder="Contenido del correo..."><?= htmlspecialchars($_POST['cuerpo'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- VCARD -->
                        <div class="form-section <?= (isset($_POST['tipo']) && $_POST['tipo']=='vcard') ? 'activo':'' ?>" id="form-vcard">
                            <h6 class="text-info mb-3">👤 Contacto (VCard)</h6>
                            <div class="form-row">
                                <div class="form-group col-12 col-md-6">
                                    <label>Nombre completo</label>
                                    <input class="form-control" type="text" name="nombre"
                                        placeholder="Pedro López"
                                        value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Teléfono</label>
                                    <input class="form-control" type="tel" name="tel_vcard"
                                        placeholder="(747) 123-4567"
                                        value="<?= htmlspecialchars($_POST['tel_vcard'] ?? '') ?>">
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Correo electrónico</label>
                                    <input class="form-control" type="email" name="email_vcard"
                                        placeholder="ejemplo@correo.com"
                                        value="<?= htmlspecialchars($_POST['email_vcard'] ?? '') ?>">
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Empresa <small class="text-muted">(opcional)</small></label>
                                    <input class="form-control" type="text" name="empresa"
                                        placeholder="Nombre de la empresa"
                                        value="<?= htmlspecialchars($_POST['empresa'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- WIFI -->
                        <div class="form-section <?= (isset($_POST['tipo']) && $_POST['tipo']=='wifi') ? 'activo':'' ?>" id="form-wifi">
                            <h6 class="text-info mb-3">📶 WiFi</h6>
                            <div class="form-row">
                                <div class="form-group col-12 col-md-6">
                                    <label>Nombre de la red (SSID)</label>
                                    <input class="form-control" type="text" name="ssid"
                                        placeholder="MiRedWiFi"
                                        value="<?= htmlspecialchars($_POST['ssid'] ?? '') ?>">
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Contraseña</label>
                                    <input class="form-control" type="text" name="wifi_pass"
                                        placeholder="Contraseña"
                                        value="<?= htmlspecialchars($_POST['wifi_pass'] ?? '') ?>">
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label>Tipo de seguridad</label>
                                    <select class="form-control" name="seguridad">
                                        <option value="WPA"    <?= (($_POST['seguridad'] ?? 'WPA') == 'WPA')    ? 'selected':'' ?>>WPA / WPA2</option>
                                        <option value="WEP"    <?= (($_POST['seguridad'] ?? '') == 'WEP')       ? 'selected':'' ?>>WEP</option>
                                        <option value="nopass" <?= (($_POST['seguridad'] ?? '') == 'nopass')    ? 'selected':'' ?>>Sin contraseña</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-6 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="oculto" id="oculto"
                                            <?= isset($_POST['oculto']) ? 'checked':'' ?>>
                                        <label class="form-check-label" for="oculto">Red oculta</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-row align-items-end justify-content-center">
                            <div class="form-group col-12 col-sm-5 col-md-4">
                                <label>Tamaño de la imagen</label>
                                <select class="form-control" name="tamano">
                                    <option value="5"  <?= (($_POST['tamano'] ?? '10') == '5')  ? 'selected':'' ?>>Pequeño  (5 px/módulo)</option>
                                    <option value="10" <?= (($_POST['tamano'] ?? '10') == '10') ? 'selected':'' ?>>Mediano  (10 px/módulo)</option>
                                    <option value="15" <?= (($_POST['tamano'] ?? '10') == '15') ? 'selected':'' ?>>Grande   (15 px/módulo)</option>
                                    <option value="20" <?= (($_POST['tamano'] ?? '10') == '20') ? 'selected':'' ?>>Extra grande (20 px/módulo)</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-sm-5 col-md-4 text-center">
                                <button type="submit" name="generar" class="btn btn-info btn-lg btn-block">
                                    Generar QR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resultado -->
            <?php if ($qrGenerado): ?>
            <div class="card mb-4 qr-result">
                <div class="card-body text-center">
                    <h5 class="text-success mb-3">✅ ¡QR generado!</h5>
                    <img src="<?= htmlspecialchars($qrGenerado) ?>" alt="Código QR generado">
                    <div class="mt-3">
                        <a href="<?= htmlspecialchars($qrGenerado) ?>" download class="btn btn-success">
                            Descargar QR
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<footer>
    <p>Pedro Alberto Lopez Pacheco</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.tipo-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tipo = this.dataset.tipo;
            // Actualizar botones
            document.querySelectorAll('.tipo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Actualizar hidden input
            document.getElementById('tipo-hidden').value = tipo;
            // Mostrar formulario correcto
            document.querySelectorAll('.form-section').forEach(f => f.classList.remove('activo'));
            document.getElementById('form-' + tipo).classList.add('activo');
        });
    });
</script>
</body>
</html>
