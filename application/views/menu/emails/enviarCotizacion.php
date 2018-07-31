<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Enviar cotizacion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
  <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#f1f1f1">
    <tr>
      <td>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
          <tr>
            <td>
              <table align="center" border="0" cellpadding="30" cellspacing="0" width="600">
                <tr>
                  <td bgcolor="#ffffff">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td style="font-family: arial; color: #3c4858">
                          <font FACE="arial" color="#3c4858">
                            Estimado Sr(a)<br><br>
                            <p>Nos es muy grato dirigirnos a Usted para hacerle llegar nuestros saludos y presentarles nuestra cotización.</p>
                            <br><p>Atentamente.</p>
                          </font>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <table>
                            <tr>
                              <td style="font-family: arial; color: #3c4858"><font FACE="arial" color="#3c4858"><?= ucwords(strtolower(valueOption('EMPRESA_NOMBRE'))) ?></font></td>
                            </tr>
                            <tr>
                              <td style="font-family: arial; color: #3c4858"><font FACE="arial" color="#3c4858"><?= ucwords(strtolower(valueOption('COTIZACION_INFORMACION'))) ?></font></td>
                            </tr>
                            <tr>
                              <td style="font-family: arial; color: #3c4858"><font FACE="arial" color="#3c4858"><?= ucwords(strtolower(valueOption('EMPRESA_CORREO'))) ?></font></td>
                            </tr>
                          </table>
                        </td>
                        <td>
                          <img src="<?= base_url('recursos/img/facebook.png') ?>" width="36" height="36" alt="facebook" style="display:block;">
                          <img src="<?= base_url('recursos/img/linkedin.png') ?>" width="36" height="36" alt="linkedin" style="display:block;">
                          <img src="<?= base_url('recursos/img/youtube.png') ?>" width="36" height="36" alt="youtube" style="display:block;">
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center" style="color: #888888">
                          <br /><br /><br /><font color="#888888">Enviado por</font><br />
                          <img height="50" src="<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", '')) ?>">
                          <br />
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center" style="border: #CB9A24 1px dashed; color: #3c4858">
                          <font FACE="arial" color="#3c4858">
                            E-mail enviado desde la plataforma de ventas web. <br>Descubre como automatizar tus cotizaciones en <a href="http://www.<?= COTIZACION_MAIL ?>">www.<?= COTIZACION_MAIL ?></a>
                          </font>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center" style="color: #888888"><br><font color="#888888">&copy; <?= date("Y").' '.COTIZACION_MAIL ?> </font></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
