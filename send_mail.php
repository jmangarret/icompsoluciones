<?php
$nombre = $_POST['name'];
$email = $_POST['email'];
$telefono = $_POST['phone'];
$asunto="Contacto desde el Sitio Web";
$mensaje = $_POST['message'];
$dest ="icompsoluciones@gmail.com";

if(!empty($nombre) AND !empty($mensaje) AND !empty($email)){   
  $headers = "Content-Type: text/html; charset=iso-8859-1\n"; 
  $headers .= "From: ".$nombre." <".$email."> \r\n";            
  $mensaje="
  <table border='0' cellspacing='2' cellpadding='2'>
    <tr>
      <td width='25%'><strong>Nombre:</strong></td>
      <td width='75%'>".$nombre."</td>
    </tr>
    <tr>
      <td width='25%'><strong>Email:</strong></td>
      <td width='75%'>".$email."</td>
    </tr> 
    <tr>
      <td width='25%'><strong>Telf.</strong></td>
      <td width='75%'>".$telefono."</td>
    </tr>
    <tr>
      <td width='25%'><strong>Mensaje:</strong></td>
      <td width='75%'>".$mensaje."</td>
    </tr>
  </table>
  ";
  @mail($dest,$asunto,$mensaje,$headers);   
}
?>