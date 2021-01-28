<?php
include_once INC_DIR . "/html.inc.php";
?>
<!doctype html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <title>{TITLE}</title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
   <meta name="author" content="Sebastian Gomez (tiochan@gmail dot com)" />
   <meta name="description" content="Management service" />
   <meta name="keywords" content="Management service" />
   <style type='text/css'>
      {
         STYLE
      }
   </style>
</head>

<body text='#000000'>
   <table align='center' border='0' cellpadding='2' cellspacing='2' width="90%">
      <tbody>
         <tr>
            <td colspan='2'>&nbsp;</td>
         </tr>
         <tr>
            <td colspan='2'>
               <table align='center' bgcolor='#335c85' border='0' cellpadding='1' cellspacing='0' height='300' width='100%'>
                  <tbody>
                     <tr>
                        <td>
                           <table bgcolor='#ffffff' border='0' cellpadding='6' cellspacing='6' height='100%' width='100%'>
                              <tbody>
                                 <tr>
                                    <td align='center' valign='top'>
                                       <center>
                                          <table border='0' cellpadding='0' cellspacing='0' width='97%'>
                                             <tbody>
                                                <tr>
                                                   <td width='60'>
                                                      <div align='right'>
                                                         <{IMAGE {SYSHOME}/{APP_MINILOGO}}> </div> </td> <td valign='middle'>
                                                            <h3>&nbsp;&nbsp;&nbsp;{PAGE_TITLE}</h3>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                   <td colspan='2'>
                                                      <hr />
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <td colspan='2'>
                                                      <br />
                                                      <br />
                                                      {PAGE_CONTENT}
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </center>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
</body>

</html>