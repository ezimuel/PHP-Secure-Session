# Encrypt PHP session data using files

The encryption is built using mcrypt extension 
and the randomness is managed by openssl
The default encryption algorithm is Rijndael-256
and we use CBC+HMAC (Encrypt-then-mac)

## WARNING

Due to the issue reported in [#19](https://github.com/ezimuel/PHP-Secure-Session/issues/19)
the current SecureSession handler implementation can be affected by **race-condition**
for concurrent session requests coming from the same SESSION_ID (for instance, with Ajax calls).

**I DO NOT SUGGEST TO USE THIS CODE IN PRODUCTION** until the issue [#19](https://github.com/ezimuel/PHP-Secure-Session/issues/19) will be  fixed!

## How to use it

Include the SecureSession.php in your project and use
the PHP session as usual.

## Demo

In the demo folder you can see a simple PHP script (demo.php)
that stores some data in $_SESSION and display the encrypted
data of the session file (stored in the temporary directory
of the operating system, e.g. /tmp in GNU/Linux).

  
Enrico Zimuel (enrico@zimuel.it)
Copyright GNU General Public License
