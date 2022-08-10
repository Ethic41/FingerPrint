<?php
/* 
Note: this file is just a proof of concept, in real application
you should handle your environment variables and secrets appropriately,
do not store in clear text or repositories
*/
putenv("FP_CLIENT_SERVICE_HOST=http://localhost:5555");

/* note this secret key is just for proof of concept,
do not use it in your production application, for
your applications generate a new secret key using
a cryptographic random number generator e.g
*openssl rand -hex 32*
or any other method and then store it in your secret
vault
*/
putenv("SECRET_KEY=secret_key_goes_here");
