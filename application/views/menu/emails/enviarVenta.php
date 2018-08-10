<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Enviar venta</title>
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
                            Estimado cliente <?= $razon_social ?>, <br><br>
                            <p>Te ha llegado un nuevo comprobante a través de nuestro sistema.</p>
                            <br><p>Atentamente.</p>
                          </font>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center">
                          <br /><br />
                          <font FACE="arial" color="#888888">
                            Descubre más de nuestra plataforma en <a href="http://www.<?= COTIZACION_MAIL ?>">www.<?= COTIZACION_MAIL ?></a>
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
