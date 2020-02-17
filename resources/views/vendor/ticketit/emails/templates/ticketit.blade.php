<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <!-- The title tag shows in email notifications, like Android 4.4. -->

    <!-- Web Font / @font-face : BEGIN -->
    <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->

    <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
    <!--[if mso]>
<style>
* {
font-family: sans-serif !important;
}
</style>
<![endif]-->

    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    <!--[if !mso]><!-->
    <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
    <!--<![endif]-->

    <!-- Web Font / @font-face : END -->

    <!-- CSS Reset : BEGIN -->
    <style>

      /* What it does: Remove spaces around the email design added by some email clients. */
      /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
      html,
      body {
        margin: 0 auto !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
      }

      /* What it does: Stops email clients resizing small text. */
      * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
      }

      /* What it does: Centers email on Android 4.4 */
      div[style*="margin: 16px 0"] {
        margin: 0 !important;
      }

      /* What it does: Stops Outlook from adding extra spacing to tables. */
      table,
      td {
        mso-table-lspace: 0pt !important;
        mso-table-rspace: 0pt !important;
      }

      /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
      table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 auto !important;
      }
      table table table {
        table-layout: auto;
      }

      /* What it does: Uses a better rendering method when resizing images in IE. */
      img {
        -ms-interpolation-mode:bicubic;
      }

      /* What it does: A work-around for email clients meddling in triggered links. */
      *[x-apple-data-detectors],  /* iOS */
      .x-gmail-data-detectors,    /* Gmail */
      .x-gmail-data-detectors *,
      .aBn {
        border-bottom: 0 !important;
        cursor: default !important;
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
      }

      /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
      .a6S {
        display: none !important;
        opacity: 0.01 !important;
      }
      /* If the above doesn't work, add a .g-img class to any image in question. */
      img.g-img + div {
        display: none !important;
      }

      /* What it does: Prevents underlining the button text in Windows 10 */
      .button-link {
        text-decoration: none !important;
      }

      /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
      /* Create one of these media queries for each additional viewport size you'd like to fix */

      /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
      @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
        .email-container {
          min-width: 320px !important;
        }
      }
      /* iPhone 6, 6S, 7, 8, and X */
      @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
        .email-container {
          min-width: 375px !important;
        }
      }
      /* iPhone 6+, 7+, and 8+ */
      @media only screen and (min-device-width: 414px) {
        .email-container {
          min-width: 414px !important;
        }
      }

    </style>
    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

      /* What it does: Hover styles for buttons */
      .button-td,
      .button-a {
        transition: all 100ms ease-in;
      }
      .button-td:hover,
      .button-a:hover {
        background: #ffffff !important;
        border-color: #ffffff !important;
      }

      /* Media Queries */
      @media screen and (max-width: 600px) {

        .email-container {
          width: 100% !important;
          margin: auto !important;
        }

        /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
        .fluid {
          max-width: 100% !important;
          height: auto !important;
          margin-left: auto !important;
          margin-right: auto !important;
        }

        /* What it does: Forces table cells into full-width rows. */
        .stack-column,
        .stack-column-center {
          display: block !important;
          width: 100% !important;
          max-width: 100% !important;
          direction: ltr !important;
        }
        /* And center justify these ones. */
        .stack-column-center {
          text-align: center !important;
        }

        /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
        .center-on-narrow {
          text-align: center !important;
          display: block !important;
          margin-left: auto !important;
          margin-right: auto !important;
          float: none !important;
        }
        table.center-on-narrow {
          display: inline-block !important;
        }

        /* What it does: Adjust typography on small screens to improve readability */
        .email-container p {
          font-size: 17px !important;
        }

      }
      

    </style>


  </head>

  <body width="100%" bgcolor="#222222" style="margin: 0; mso-line-height-rule: exactly; background-color: #001728;">
    <center style="width: 100%; text-align: left; margin-bottom: 100px !important;">

      <!-- Preview Text Spacing Hack : BEGIN -->
      <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
      </div>
      <!-- Preview Text Spacing Hack : END -->

      <!-- Email Header : BEGIN -->
      <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">
        <tr>
          <td style="padding: 20px 0; text-align: center;">
            <img src="{{ url('/logo.png')}}" alt="alt_text" border="0" style="height: auto; background: #001728; font-family: sans-serif; font-size: 15px; line-height: 140%; max-width: 200px; margin-top: 50px">
          </td>
        </tr>
      </table>
      <!-- Email Header : END -->

      <!-- Email Body : BEGIN -->
      <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="margin: auto;" class="email-container">

        <!-- 1 Column Text + Button : BEGIN -->
        <tr>
          <td bgcolor="#ffffff" style="padding: 40px 40px 20px; text-align: center; font-size: 35px; font-weight: 600">
            @yield('subject')
             </td>
        </tr>
        <tr>
          <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #001728; text-align: center;">
            @yield('content')
          </td>
        </tr>
        <tr>
          <td style="color:#001728; background-color: #ffffff;  display:flex; flex-direction: column; justify-content: center;  text-align: center">
            {{ $setting->grab('email.signoff') }}<br>
            {{ $setting->grab('email.signature') }}
            <br><br>
          </td>
        </tr>
        <tr>
          <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 140%; color: #ffffff;">
            <!-- Button : BEGIN -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: auto">
              <tr>
                <td style="border-radius: 3px; background: #222222; text-align: center;" class="button-td">
                  @yield('link')
                </td>
              </tr>
            </table>    
            <!-- Button : END -->
          </td>
        </tr>
        <!-- 1 Column Text + Button : END -->
        <tr>
          <td bgcolor="#ffffff" style="padding: 0 40px 40px; font-family: sans-serif; font-size: 10px; line-height: 140%; color: #001728; text-align: center;">
            <p>This message was sent to <b>info@cargofive.com</b>. If you do not want to receive more emails, please tell us.</p>
            <h4 style="text-align: center;"> CARGOFIVE. Lisbon, Portugal.</h4>
            <h4 style="text-align: center;">info@cargofive.com </h4>
          </td>
        </tr>

        <tr>
          <td style="display: flex;justify-content:center;text-align: center;">
            <a style="color: #ffffff; text-decoration:none; font-size: 12px; margin-top: 50px;" href="{{ $setting->grab('email.footer_link') }}">
              {{ $setting->grab('email.footer') }}
            </a>
            <br>
            <br>
          </td>
        </tr>
      </table>
      <!-- Email Body : END -->
    </center>
  </body>
</html>