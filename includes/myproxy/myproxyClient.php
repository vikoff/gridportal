<?php

/******************************************************************* 
* myproxyClient.php Beta
* Native PHP function for anonymous retrieval of credentials from a myproxy server.
* Created on January 18, 2008
* Stephen Mock,
* mock@tacc.utexas.edu
* http://www.tacc.utexas.edu/
*******************************************************************
UT TACC Public License - Version 1.0 (UTTPL v1.0)
Copyright 2004, The University of Texas at Austin

RECITALS
The Texas Advanced Computing Center of The University of Texas at Austin has 
developed certain software and documentation which it desires to make available to 
anyone for any purpose through this open source license.

1. DEFINITIONS.
1.1 License shall mean the grant of rights hereunder referred to herein as the UTTPL.
1.2 Licensor shall mean the copyright owner or other authorized entity granting 
rights hereunder.
1.3 Licensee shall mean persons or entities exercising rights granted hereunder.
1.4 Software shall mean the computer program(s) referred to as GridPort made 
available under this License in object code or source code form.
1.5 Documentation shall mean all manuals, user documentation, and other related 
materials pertaining to the Software which are made available to Licensee in 
connection with the Software.
1.6 Contribution shall mean any work, including the original version of the Software 
and any modifications to that Software or Derivative Products thereof.
1.7 Contributors shall mean persons or entities that have made a Contribution to 
Software or Documentation.
1.8 Derivative Products shall mean computer programs in machine readable object 
code or source code form which are a modification of, enhancement to, derived from 
or based upon Software or Documentation.

2. GRANT OF RIGHTS.
2.1 Subject to the terms and conditions hereunder, Licensor hereby grants to Licensee
a perpetual, worldwide, non-exclusive license (i) to use and modify the Software to 
create Derivative Products and (ii) to install use, manufacture, reproduce, display,
sublicense, market and distribute the Documentation and the Software.

3. REDISTRIBUTIONS.
3.1 Licensee may distribute Software, Documentation or Derivative Products that 
include a copy of the UTTPL.  Modified files of such redistributions must provide 
notice that the file was altered.  All copyright, patent, trademark and 
acknowledgement notices must be retained in Software, Documentation and Derivative 
Products.

4. PROTECTION OF SOFTWARE.
4.1 Proprietary Notices. Licensee shall maintain and place on any copy of Software, 
Derivative Products or Documentation which it reproduces, whether for internal use 
or for distribution, all such notices as are authorized and/or required hereunder.  
Licensee shall include a copy of this UTTPL and the following notice, on each copy 
of the Software and Documentation. Such license and notice shall be loaded in the 
computer memory for use, display, or reproduction and shall be embedded in program 
source code and object code, in the video screen display, on the physical medium 
embodying the Software copy, Derivative Products and on any Documentation and 
sublicensee reference manuals:
Copyright ? The University of Texas, 2004. All rights reserved.
UNIVERSITY EXPRESSLY DISCLAIMS ANY AND ALL WARRANTIES CONCERNING THIS SOFTWARE AND 
DOCUMENTATION, INCLUDING ANY WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR ANY 
PARTICULAR PURPOSE, AND WARRANTIES OF PERFORMANCE, AND ANY WARRANTY THAT MIGHT 
OTHERWISE ARISE FROM COURSE OF DEALING OR USAGE OF TRADE. NO WARRANTY IS EITHER 
EXPRESS OR IMPLIED WITH RESPECT TO THE USE OF THE SOFTWARE OR DOCUMENTATION. Under 
no circumstances shall University be liable for incidental, special, indirect, 
direct or consequential damages or loss of profits, interruption of business, or 
related expenses which may arise from use of software or documentation, including 
but not limited to those resulting from defects in software and/or documentation, 
or loss or inaccuracy of data of any kind.

5. WARRANTIES.
5.1 Disclaimer of Warranties.  TO THE EXTENT PERMITTED BY APPLICABLE LAW LICENSOR 
AND ALL CONTRIBUTORS PROVIDE SOFTWARE, DERIVATIVE PRODUCTS AND DOCUMENTATION ON AN 
AS IS BASIS WITHOUT ANY WARRANTIES OF ANY KIND RESPECTING THE SOFTWARE, DERIVATIVE 
PRODUCTS OR DOCUMENTATION PROVIDED HEREUNDER, EITHER EXPRESS OR IMPLIED, INCLUDING 
BUT NOT LIMITED TO ANY WARRANTY OF DESIGN, MERCHANTABILITY, OR FITNESS FOR A 
PARTICULAR PURPOSE.
5.2 Limitation of Liability. UNDER NO CIRCUMSTANCES UNLESS REQUIRED BY APPLICABLE 
LAW SHALL LICENSOR OR ANY CONTRIBUTOR BE LIABLE FOR INCIDENTAL, SPECIAL, INDIRECT, 
DIRECT OR CONSEQUENTIAL DAMAGES OR LOSS OF PROFITS, INTERRUPTION OF BUSINESS, OR 
RELATED EXPENSES WHICH MAY ARISE AS A RESULT OF THIS LICENSE OR OUT OF THE USE OR 
ATTEMPT OF USE OF SOFTWARE, DERIVATIVE PRODUCTS OR DOCUMENTATION INCLUDING BUT NOT 
LIMITED TO THOSE RESULTING FROM DEFECTS IN SOFTWARE AND/OR DOCUMENTATION, OR LOSS OR
INACCURACY OF DATA OF ANY KIND.  THE FOREGOING EXCLUSIONS AND LIMITATIONS WILL APPLY 
TO ALL CLAIMS AND ACTIONS OF ANY KIND, WHETHER BASED ON CONTRACT, TORT (INCLUDING, 
WITHOUT LIMITATION, NEGLIGENCE), OR ANY OTHER GROUNDS.

6. INDEMNIFICATION. 
6.1 Licensee shall indemnify and hold harmless Licensor, any Contributor, the 
copyright holders, their officers, agents and employees from and against any claims, 
demands, or causes of action whatsoever, including without limitation those arising 
on account of Licensees modification or enhancement of the Software or any 
Derivative Products or otherwise caused by, or arising out of, or resulting from, 
the exercise or practice of the license granted hereunder by Licensee, its 
sublicensees, if any, its subsidiaries or their officers, employees, agents or 
representatives.

7. USE OF NAME.
7.1 Licensee may use the name The University of Texas at Austin in factually based 
materials related to Software, Derivative Products and Documentation and the business
of the Licensee; provided, however, that Licensee may not use the name of The 
University of Texas at Austin in connection with any name, brand or trademark 
related to Software, Derivative Products or Documentation. For example, Licensee 
may include a statement in promotional materials that refers to the fact that a 
product or service is based on technology developed at The University of Texas at 
Austin; Licensee may not include The University of Texas at Austin in a product or 
service name.
END OF TERMS AND CONDITIONS

The University of Texas at Austin       UTTPL v1.0, February 15, 2004
Texas Advanced Computing Center      
*******************************************************************

* Thanks to and help from Jim Basney.
* http://grid.ncsa.uiuc.edu/myproxy/
*
* This code REQUIRES the PHP openssl module:
* http://php.net/manual/en/ref.openssl.php
* This requires php to be installed with openssl support
*
* Args:
* 1-myproxy server - string containing of the host name of the myproxy server. For example
*   myproxy.teragrid.org
* 2-myproxy port - int port number or null to use default port 7512
* 3-myproxy username - string containing of the username that the cert is stored under on 
*   the myproxy server
* 4-myproxy passphrase - string containing the passphrase used to store the cert on the myproxy
*   server
* 5-lifetime - time in seconds that the delegated proxy will be good for
* 6-outfile - string containing the full path to the outfile to be created
* 7-DEBUG - boolean whether or not to print debugging messages
*
* Returns - true if successful, false if not
*
* Example:
* include('myproxyClient.php');
* if(myproxy_logon('myproxy.teragrid.org', 7512, 'homersimpson', 'dohdohdoh!', 1024, '/tmp/x509up_u501',false)) {
*   //do logged in stuff
* } else {
*   //do login error stuff
* }
*******************************************************************/
function myproxy_logon ($myproxy_server, $myproxy_port, $username, $passphrase, $lifetime, $outfile, $DEBUG) {
    if(!$DEBUG) {
        $DEBUG = false;
    }
    if( (!$username) || (!$passphrase) || (!$outfile) || (!$lifetime)) {
        if($DEBUG) { echo "\n<br>An argument to myrproxy_logon was not properly set."; }
        return false;
    }
    if(! $myproxy_server) {
        $myproxy_server = 'localhost';
    }
    if(!$myproxy_port) {
        $myproxy_port = 7512;
    }
    
    //hostname of the myproxy server. should be in form 'tcp://myproxy.teragrid.org'
    $MYPROXY_SERVER = 'tcp://' . $myproxy_server;
    $PORT = $myproxy_port; //default myproxy port is 7512
    $USERNAME = $username;
    $PASSPHRASE = $passphrase;
    $OUTFILE = $outfile;
    
    
    //assemble the myproxy get command
    $CERT_OUT = "";
    $CMD_GET =
        "VERSION=MYPROXYv2\n" .
        "COMMAND=0\n" .
        "USERNAME=$USERNAME\n" .
        "PASSPHRASE=$PASSPHRASE\n" .
        "LIFETIME=$lifetime\n";
        
    //Distinguished Name, this info does not matter, as the myproxy server will replace it with 
    //the correct info in the certificate it returns
    $DN=array(
    "countryName" => "US",
    "stateOrProvinceName" => "Unknown",
    "localityName" => "Springfield",
    "organizationName" => "Springfield Nuclear Power Plant",
    "organizationalUnitName" => "Sector 7-G Safety Inspection",
    "commonName" => "Homer Simpson",
    "emailAddress" => "homer@springfieldnuclear.com"
    );
    
    //OpenSSL configuration for generating a private key
    $SSL_CONFIG = array (
        "private_key_bits"=>"1024",
        "digest_alg"=>"md5",
        "encrypt_key" => false,
        "private_key_type"=>"OPENSSL_KEYTYPE_RSA"
    );
    
    //generate private key
    $privkey=openssl_pkey_new();
    //exports text to $privkey_string
    openssl_pkey_export($privkey,$privkey_string);
    //generate new CSR(certificate signing request) using privkey, DN, and config
    $csr = openssl_csr_new($DN,$privkey,$SSL_CONFIG);
    
    //export the CSR to $csr_data as text
    $publickeyString = openssl_csr_export($csr,$csr_data);
    
    //convert the CSR to DER format that myproxy expects
    $der_csr = pem2der($csr_data);
    
    //open a normal socket connection to the server
    $fd=fsockopen( $MYPROXY_SERVER, $PORT, $errno, $errstr);
    if(!$fd) {
        if($DEBUG) { 
            echo "\n<br>Could not create socket connection to $MYPROXY_SERVER: ". $errno.":".$errstr; 
        }
        return false;
    }
    
    //convert normal socket to an SSL v3 Client socket connection
    if(stream_socket_enable_crypto( $fd, true, STREAM_CRYPTO_METHOD_SSLv3_CLIENT ) === false) {
        fclose($fd);
        if ($DEBUG) { echo "<br>Unable to establish SSLv3 Connection with $MYPROXY_SERVER"; }
        return false;
    }
    if($fd === false) {
        fclose($fd);
        if ($DEBUG) { echo "<br>Unable to establish SSLv3 Connection with $MYPROXY_SERVER"; }
        return false;
    }
    
    if($DEBUG) { echo "\n<br>SSLv3 connection established with $MYPROXY_SERVER.<br>"; }
    
    if(fwrite($fd,'0') == true) { //send Globus Compatibility Zero byte
        if(fwrite($fd, $CMD_GET) == true) { //send GET COMMAND
            $dat = "";
            $dat .= fgets($fd); //read two lines
            $dat .= fgets($fd);
            if(strpos($dat,"RESPONSE=0") === false) {
                if($DEBUG) { echo "\n<br>Server reponse: $dat"; }
                fclose($fd);
                return false;
            }
            
            if($DEBUG) { echo "\n<br>read $dat from myproxy server<br>"; }
            fread($fd,1); //get null termination
    
            //send the cert request
            $csr_send = fwrite($fd,$der_csr);
            
            if($DEBUG) { echo "\n<br>sent $csr_send bytes<br>"; }
            
            //receive response containing the certs
            while(!feof($fd)) {
                $buf = "";
                $buf = fread($fd,8192);
                if(strpos($buf,'VERSION=MYPROX') === false) {
                    $CERT_OUT = $CERT_OUT . $buf;
                } else {
                    $buf = "";
                }
            }
            
            //set umask super restrictive so file gets created with user read/write only
            $oldmask = umask(0177);
            if(strlen($CERT_OUT) > 0) { 
                $pemArray = der2pem($CERT_OUT);
                $fh = fopen($OUTFILE,'w') or die("\n<br>Could not open proxy file for writing.");
                fwrite($fh, $pemArray[0]);
                fwrite($fh,$privkey_string);
                for($n=1; $n < count($pemArray); $n++) {
                    fwrite($fh,$pemArray[$n]);
                }
                fclose($fh);
                umask($oldmask);
                return true;
            } else {
                fclose($fd);
                if($DEBUG) { echo "\n<br>No certificate recieved from $MYPROXY_SERVER"; }
                return false;
            }
        }
    } else {
        fclose($fd);
        if($DEBUG) { echo "\n<br>Could not write to SSL socket at $MYPROXY_SERVER"; }
        return false;
    }
    
    fclose($fd);
    return true;
    

}


//converts PEM cert info to DER cert info
function pem2der($pem_data) {
    $begin = "CERTIFICATE REQUEST-----";
    $end = "-----END";
    $pem_data = substr($pem_data, strpos($pem_data, $begin)+strlen($begin));
    $pem_data = substr($pem_data, 0, strpos($pem_data, $end));
    $der = base64_decode($pem_data);
    return $der;
}

// unpacks certificates from myproxy server response and
// converts them to PEM format
// Arg : -string containing the myproxy server response
// Returns : -array of strings each containing a certificate
function der2pem($der_data) {
    $pems = array();
    $num_array = unpack('C',substr($der_data,0,1)); //C* converts to unsigned char
    $num_certs = $num_array[1]; //why does unpack start at index 1? why!?!?!
    
    $der_data = substr($der_data,1); //trim off the number of certs from first byte

    //now bytes 1 and 2 mark the beginning of the cert
    for($i=0; $i < $num_certs; $i++) {
        $pem = "";
        $index = 0;
        //bytes 3 and 4 tell how long the cert is
        $l1 = ord(substr($der_data,$index+2,$index+3));
        $l2 = ord(substr($der_data,$index+3,$index+4));
        $len = (256*$l1) + $l2;
    
        $thisCertData = substr($der_data,$index,$index+$len+4);
        $pem = $pem. "-----BEGIN CERTIFICATE-----\n" . chunk_split(base64_encode($thisCertData), 64, "\n")
            . "-----END CERTIFICATE-----\n";
        $der_data = substr($der_data,$index+$len+4);
        array_push($pems, $pem);
    }
    return $pems;
}
?>