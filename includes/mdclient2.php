<?php
/****************************************************************************
* include/mdclient2.php
*
* Basado en mdclient.php, añadienco compatibilidad con php4.
*
Copyright (c) 2004 on behalf of the EU EGEE Project:
The European Organization for Nuclear Research (CERN),
Istituto Nazionale di Fisica Nucleare (INFN), Italy
Datamat Spa, Italy
Centre National de la Recherche Scientifique (CNRS), France
CS Systeme d'Information (CSSI), France
Royal Institute of Technology, Center for Parallel Computers (KTH-PDC), Sweden
Universiteit van Amsterdam (UvA), Netherlands
University of Helsinki (UH.HIP), Finlan
University of Bergen (UiB), Norway
Council for the Central Laboratory of the Research Councils (CCLRC), United Kingdom
See LICENSE.mdclient

modified for amgaNavigator versión 1.0.1 on active platforms, official EGEE distribution RHSL: php4.
Copyright (C) 2009 Victor <vmendez@ific.uv.es>
Instituto de Fisica Corpuscular IFIC/CSIC.

*******************************************************************************/

class MDClient
{
	/**
	* Status of the connection
	* @access private
	* @var integer
	*/
	var $connected;

	/**
	* Hostname of the AMGA server
	* @access private
	* @var string
	*/
	var $host;

	/**
	* Port on which the AMGA server listens for connection
	* @access private
	* @var integer
	*/
	var $port;

	/**
	* Username used to access the server
	* @access private
	* @var string
	*/
	var $login;

	/**
	* Password used to access the server
	* @access private
	* @var string
	*/
	var $password;

	/**
	* Whether we want to keep the connection persistent
	* @access private
	* @var integer
	*/
	var $keepalive;

	/**
	* Buffer which contains the messages received from the server
	* @access private
	* @var string
	*/
	var $buffer;

	/**
	* Whether we want to require SSL
	* @access private
	* @var boolean
	*/
	var $reqSSL;

	/**
	* The socket used when we require ssl connection
	* @access private
	* @var string
	*/
	var $sslOptions;

	/**
	* Identifier of the session
	* @access private
	* @var integer
	*/
	var $sessionID;

	/**
	* Name of the session
	* @access private
	* @var integer
	*/
	var $session;

	/**
	* Greetings message
	* @access private
	* @var string
	*/
	var $greetings;

	/**
	* Version of the protocol
	* @access private
	* @var integer
	*/
	var $protocolVersion;

	/**
	* String of the command to execute
	* @access private
	* @var string
	*/
	var $currentCommand;

	/**
	* Path of the user's key
	* @access private
	* @var string
	*/
	var $keyFile;

	/**
	* Path of the user's certificate
	* @access private
	* @var string
	*/
	var $certFile;

	/**
	* Socket used to talk to the AMGA server
	* @access private
	* @var resource
	*/
	var $socket;

	/**
	* Debug mode
	* @access private
	* @var boolean
	*/
	var $debug;

	/**
	* End Of Transmission
	* @access private
	* @var integer
	*/
	var $EOT;

	/**
	* Number of attributes
	* @access private
	* @var integer
	*/
	var $nattrs;

	/**
	* error message avoiding exception method for php4
	* @access public
	* @var string
	*/
	var $err_msg;

	/**
	* to store the working directory
	* @access public
	* @var string
	*/
	var $act_directory;

	/**
	* to store the user home directory on amga
	* @access public
	* @var string
	*/
	var $home_directory;

	/**
	* Constructor method
	* @access public
	* @param boolean $keepalive whether we want to keep the connection persistent
	* @return void
	*/
	function MDClient($keepalive = true)
	{
		$this->connected = 0;
		// on initialize function:
		//$this->host = $host;
		//$this->port = $port;
		//$this->login = $login;
		//$this->password = $password;
		$this->keepalive = $keepalive;
		$this->buffer = "";
		$this->reqSSL = false;
		$this->sslOptions = "";
		$this->sessionID = 0;
		$this->session = "";
		$this->greetings = "";
		$this->protocolVersion = 0;
		$this->currentCommand = "";
		$this->debug = false;
	}

	/**
	* complementary initialize, to construct and instantiate the variable
	* on this class definition
	* @param string $host AMGA server hostname
	* @param integer $port AMGA server port
	* @param string $login username used to access AMGA
	* @param string $password password used to access AMGA
	*/
	function InitializeMDClient($host = "localhost", $port = "8822", $login = "arda", $password = "")
	{
		$this->host = $host;
		$this->port = $port;
		$this->login = $login;
		$this->password = $password;
	}

	/**
	* Function used to enable SSL connection
	* @access public
	* @param string $key path of the user's key
	* @param string $cert path of the user's certificate
	* @return void
	*/
	function requireSSL($key, $cert, $capath = "certificates")
	{
		$this->reqSSL = true;
		$this->keyFile = $key;
		$this->certFile = $cert;
		$this->caPath = $capath;
	}

	/**
	* SSL handshake function
	* @access private
	* @param string $session name of the session
	* @return void
	*/
	function doSSLHandshake($session = "")
	{
		if ($this->debug)
			echo "Doing SSL handshake using builtin SSL<br>";

		stream_context_set_option($this->socket, 'ssl', 'local_cert', $this->certFile);
		stream_context_set_option($this->socket, 'ssl', 'capath', $this->caPath);
		stream_context_set_option($this->socket, 'ssl', 'allow_self_signed', true);
		stream_context_set_option($this->socket, 'ssl', 'verify_peer', true);
		
		stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
	}

	/**
	* Function used to connect to the AMGA server
	* @access public
	* @return void
	*/
	function connect()
	{
		if(!($this->socket = fsockopen($this->host, $this->port)))
		{
			//throw new Exception("Error while creating socket");
			$this->err_msg .= "Error while creating socket";
			return 0;
		}

		if ($this->debug)
			echo "Connected to ".$this->host.":".$this->port."<br>";
		
		$this->greetings = "";
		while (substr_count($this->greetings, "\n") < 3)
		{
			if (!($line = fgets($this->socket, 1024)))
			{
				//throw new Exception("Error while receiving data from socket");
				$this->err_msg .= "Error while receiving data from socket";
				return 0;
			}
			$this->greetings .= $line;
		}

		if ($this->debug)
			echo $this->getGreetings()."<br>";

		
		if ($pos = strpos($this->greetings, "\nProtocol"))
		{
			$this->protocolVersion = substr($this->greetings, $pos+10, 1);
		}		

		if ($this->sessionID)
		{
			if ($this->debug)
				echo "Trying to resume session ".$this->sessionID."<BR>";

			// Do reconnect
			if ($this->reqSSL)
			{
				fwrite($this->socket, "resumeSSL%u\n\n".$this->sessionID);
				$line = fgets($this->socket, 1024); // OK from server
				$this->doSSLHandshake($this->session);
			}
			else
			{
				fwrite($this->socket, "resume%d\n\n".$this->sessionID);
				// yzz dixit:
				$line = fgets($this->socket, 1024); // OK from server
				if ($this->debug)
					echo "resume session server say: ".$line."<BR>";
				// yzz dixit: Aunque el servidor responde OK, luego da muchos problemas, mejor como sesion nueva.
				
				$this->connected = 1;
				$this->buffer = "";
				return $this->connected;
				// return true;
			}
		}
		else
		{
			if ($this->reqSSL)
			{
				if ($this->debug)
					echo "Trying to establish new SSL session<br>";

				fwrite($this->socket, "ssl\n\n");
				$line = fgets($this->socket, 1024); // OK from server
				$this->doSSLHandshake();
			}
			else
			{
				if ($this->debug)
					echo "Trying to establish new plain session<br>";

				fwrite($this->socket, "plain\n\n");
				$line = fgets($this->socket, 1024); // OK from server
			}
			if ($this->debug)
				echo "[connect function] Server sent: ".$line."<br>";
		}
		
		// Send login information if not doing reconnect 
		if (!$this->sessionID)
		{
			$context = "0 ". $this->login . "\n5 " . $this->password . "\n\n";
			fwrite($this->socket, $context);
		}

		$this->connected = 1;
		if ($this->debug)
			echo "[function connect()], connected==1 <br>";
		$this->buffer = "";
		return $this->connected;
	}

	/**
	* Function used to disconnect from the AMGA server
	* @access public
	* @param string $saveSession whether to save the session
	* @return void
	*/
	function disconnect($saveSession = false)
	{
		if ($saveSession)
		{
			$this->session = "";
		}

		if ( $this->connected == 1)
		{
			fclose($this->socket);
			$this->connected = 0;
		}
	}
	
	/**
	* Function used to send a command to the AMGA server
	* @access private
	* @param string $command text of the command
	* @return void
	*/
	function sendCommand($command)
	{
		fwrite($this->socket, $command . "\n");
	}

	/**
	* Function used to execute a command
	* @access public
	* @param string $command text of the command
	* @return retValue if the command executes successfully, 0 otherwise
	*/
	function execute($command)
	{
		if ($this->debug)
			echo "[execute] Sending ". $command."<br>";
		
        	$this->currentCommand = $command;        
	        $this->buffer = "";

        	if (!$this->keepalive)
            		$this->disconnect();

        	if ( $this->connected==0 )
        	{   
			if ($this->debug)
        		{   
				echo "[execute]entro por connected==0<br>";
			}
			if ($this->connect() == 0)
				return 0;
		}

		if ($this->debug)
        	{   
			echo "[execute]this->connected=".$this->connected."<br>";
			echo "[execute]previo sendCommand<br>";
		}
		
        	$this->sendCommand($command);
		
        	return $this->retrieveResult();
	}

	/**
	* Retrieves the result of the remote call. The result is the first
    * line sent by the server. If an error occurred, the rest of the
    * data sent by the server is read until EOT is found.
	* @access public
	* @return string if the command executes successfully, false otherwise
	*/
	function retrieveResult()
	{
		$this->EOT = 0;
        	$this->buffer = "";
		
		$line = $this->fetchRow();
		if ( $this->debug )
		{
		        echo "(RetrieveResult): fetchRow return: ".$line."<br>";
		}

		if ($line == "")
		{
			//throw new Exception("Server sent empty response");
			$this->err_msg .= "Server sent empty response";
			return false;
		}

		$pos = strpos($line, " ");
		$msg = "";
		if ($pos !== false)
		{
			//$retValue = substr($line, $pos, 1);
			// yzz dixit:
			$retValue = substr($line, 0, $pos);
			$msg = substr($line, $pos + 1);
		}
		else
		{
			$retValue = $line;
			$msg = $line;
		}

		if ( $this->debug )
		{
		        echo "(RetrieveResult): line: ".$line."<br>";
		        echo "(RetrieveResult): retValue: ".$retValue."<br>";
		}

		if ($retValue != 0)
		{
			if ($this->debug)
				echo "The command did not execute correctly - return value: ".$retValue."<br>";

            		// The command did not execute properly. Clear
            		// the input buffer and raise an exception
			while (!$this->EOT)
			{
				if ($this->fetchData() < 0)
					break;
			}
	        	//throw new Exception($msg);
			$this->err_msg = " Error_num: " . $retValue . " Error_msg: " .  $msg . "  ( " . $this->currentCommand . " )"; 
			$this->buffer = "";
		}
		
		return $retValue;
	}

	/**
	* Function used to execute commands without waiting for the answer
	* it doesn't wait for any return condition of the remote command
	* @access public
	* @return string if the command executes successfully, false otherwise
	*/
	function executeNoWait($command)
	{
		if ($this->debug)
			echo "[Sending] ".$command."<br>";
		$this->buffer = "";
        	if (!$this->keepalive)
            		$this->disconnect();
        	if ($this->connected == 0 )
		{
			if ($this->connect()==false)
				return false;
		}
        	$this->sendCommand($command);
        
		if ($this->dataArrived())
            		return $this->retrieveResult();
	}

	/**
	* Reads a row from the buffer, if necessary the buffer  
	* is first filled by reading from the server
	* @access public
	* @return string a single row from the output buffer
	*/
	function fetchRow()
	{
		if ($this->debug)
			echo "Fetching a row...<br>";

		$pos = strpos($this->buffer, "\n");
	        if ($pos !== false)
		{
       		        $line = substr($this->buffer, 0, $pos);

			if ($this->debug)
				echo "First line of buffer: ".$line."<br>";
            
			$this->buffer = substr($this->buffer, $pos + 1);
            		if (!strlen($this->buffer) && !$this->EOT)
			{
                		if ($this->fetchData() < 0)
				{
					return "";
				}
			}
			
            		return $line;
		}
		
        	if ($this->EOT)
		{
			if ($this->debug)
				echo "found EOT<br>";

			return "";
		}
        	if ($this->fetchData() <= 0) 
		{
			if ($this->debug)
				echo "no data from fetchData<br>";
            
			return "";
		}

		return $this->fetchRow();
	}

	/**
	* Fetches more data from the server until a full line is 
	* in the buffer or an EOT is detected
	* @access public
	* @return integer the number of lines fetched
	*/
	function fetchData()
	{
		stream_set_blocking($this->socket, 0);
		if ($this->debug)
		{
			echo "Starting data fetch...<br>";
		}

		while (true)
		{
			$pos = strpos($this->buffer, 4);
			if ($pos !== false)
			{	
				if ($this->debug)
					echo "Found 004 at position ".$pos."<br>";
				
				while ($this->protocolVersion > 1 && strpos($this->buffer, 4, $pos + 1) === false)
				{
					$line = fread($this->socket, 1024);
		                    	if (!$line)
						break;

                    		$this->buffer .= $line;
				}
				break;
			}
			
			// Look for newline
			$pos = strpos($this->buffer, "\n");
			if ($pos !== false)
			{	
				if ($this->debug)
					echo "Found newline<br>";
				break;
			}
			
			// Do read to find newline
			$line = fread($this->socket, 1024);

			if (strlen($line) > 0) 
			{
				if ($this->debug)
					echo "fetchData fetched: ".$line."<br>";
			
				// No more lines
				if (!$line)
				{
					if ($this->debug)
						echo "No more lines to read!<br>";
					break;
				}
            
				$this->buffer .= $line;
			}
		}
		stream_set_blocking($this->socket, 1);

		if ($this->debug)	
		{
			echo "Buffer contents: <pre>".$this->buffer."</pre>";
		}
		
		// Check whether we are at the end of the transmission
		// Read session handle if provided and shutdown connection if there
		$pos = strpos($this->buffer, 4);
        	if ($pos !== false)
		{
	            $this->sessionID = 0;
	            if ($pos < strlen($this->buffer)-8 && substr($this->buffer, $pos + 1, $pos + 8) == "session")
			{
       		         	$pos2 = strpos($this->buffer, 4, $pos + 1);
				$this->sessionID = substr($this->buffer, $pos + 8, $pos2);
                		$this->disconnect(true);
			}
            
			if ($this->debug)
				echo "Session ID(FechtData): " . $this->sessionID."<br>";
            
			$this->buffer = substr($this->buffer, 0, $pos);
			$this->EOT = 1;
		}

        	if (!$line)
            		return -1;
		
		if ($this->debug)
			echo "fetchData returns: ".strlen($line)."<br>";

        	return strlen($line);
	}

	/**
	* Function used to check if data has arrived
	* @access private
	* @return boolean whether data has arrived
	*/
	function dataArrived()
	{
		$gotSomething = 1;

		$read = array($this->socket);
		$result = stream_select($read, $write = NULL, $except = NULL, 0);
		if ($result === false) 
		{
			$gotSomething = 0;
		}

        return $gotSomething;
	}

	/**
	* Function used to quote a string
	* @access private
	* @param string $value the string to quote
	* @return string the quoted string
	*/
	function quoteValue($value)
	{
		$value = str_replace("'", "\'", $value);
		$value = "'" . $value . "'";

		return $value;
	}

	/**
	* Function used to check if it is End Of Transmission
	* @access public
	* @return boolean whether it is End Of Transmission
	*/
	function eot()
	{
		if (strlen($this->buffer) > 0)
		{
			return false;
		}
		
		if ($this->EOT)
		{
			return true;
		}		
		
		if ($this->fetchData() <= 0)
			return true;

		return (!(strlen($this->buffer) > 0));
	}

	/**
	* Function used to get some attributes of a file
	* @access public
	* @param string $file the name of the target file
	* @param array $attributes list of attributes we want to get
	* @return 0 if OK or num_erro
	*/
	function getattr($file, $attributes)
	{
		$command = 'getattr ' . $file;
		foreach($attributes as $i)
			$command = $command . ' ' . $i;
		$this->nattrs = count($attributes);
		return $this->execute($command);
	}

	/**
	* Return name and attributes of an entry
	* @access public
	* @return array(name, array(attributes))
	*/
	function getEntry()
	{
		$file = $this->fetchRow();
		$attributes = array();
		for ($i = 0; $i < $this->nattrs; $i++)
		{
			$attr = $this->fetchRow();
			$attributes[] = $attr;
		}
        	return array($file, $attributes);
	}

	/**
	* Sets one ore more attributes of a file
	* @access public
	* @param string $file the file name of the entry
	* @param array $keys a list of keys
	* @param array $values the list of values which are assigned to the keys
	* @return 0 if OK or num_erro
	*/
	function setAttr($file, $keys, $values)
	{
        	$command = 'setattr ' . $file;
		for ($i = 0; $i < count($keys); $i++)
		{
			$command = $command . ' ' . $keys[$i];
			$values[$i] = $this->quoteValue($values[$i]);
			$command = $command . ' ' . $values[$i];
		}
		return $this->execute($command);
	}

	/**
	* Adds one entry to amga
	* @access public
	* @param string $file the file name of the entry
	* @param array $keys a list of keys
	* @param array $values the list of values which are assigned to the keys
	* @return 0 if OK or num_erro
	*/
	function addEntry($file, $keys, $values)
	{
		$command = 'addentry ' . $file;
		for ($i = 0; $i < count($keys); $i++)
		{
			$command = $command . ' ' . $keys[$i];
			$values[$i] = $this->quoteValue($values[$i]);
			$command = $command . ' ' . $values[$i];
		}
		return $this->execute($command);
	}

	/**
	* Adds some entries to amga
	* @access public
	* @param array $entries array of entries to be added
	* @return 0 if OK or num_erro
	*/
	function addEntries($entries)
	{
		$command = 'addentries';
		foreach($entries as $e)
		{
			$command = $command . ' ' . $e;
		}
		return $this->execute($command);
	}

	/**
	* Adds one attribute to a file
	* @access public
	* @param string $file the file name of the entry
	* @param string $name the name of the attribute
	* @param string $t type of the attribute
	* @return return_value (Retrieve result)
	*/
	function addAttr($file, $name, $t)
	{
		$command = 'addattr ' . $file . ' ' . $name . ' ' . $t;
		return $this->execute($command);
	}

	/**
	* Removes one attribute from a file
	* @access public
	* @param string $file the file name of the entry
	* @param string $name the name of the attribute
	* @return 0 if OK or num_erro
	*/
	function removeAttr($file, $name)
	{
		$command = 'removeattr ' . $file . ' ' . $name;
		return $this->execute($command);
	}

	/**
	* Resets the value of a given attribute of a file
	* @access public
	* @param string $file the file name of the entry
	* @param string $name the name of the attribute
	* @return 0 if OK or num_erro
	*/
	function clearAttr($file, $name)
	{
		$command = 'clearattr ' . $file . ' ' . $name;
		return $this->execute($command);
	}

	/**
	* List entries according to a specific pattern
	* @access public
	* @param string $pattern the pattern of the entries we want to get
	* @return string list of the entries
	*/
	function listEntries($pattern)
	{
		$command='dir ' . $pattern;
		$ret_val=$this->execute($command);
		$this->nattrs = 1;
		return $ret_val;
	}

	/**
	* Returns current working directory
	* @access public
	* @return string name of the current working directory
	*/
	function pwd()
	{
		//$this->execute('pwd');
		//return $this->fetchRow();
		//yzz dixit:
		return $this->execute('pwd');
	}

	/**
	* Lists the attributes of a file
	* For a given file this function returns a list of attributes of a
    * directory and their types. Note that also attributes which are
    * undefined (assigned to NULL) for the entry are listed.
	* @access public
	* @param string $file name of target file
	* @return array(array(attributes), array(types))
	*/
	function listAttr($file)
	{
		$command = 'listattr ' . $file;
		$res = $this->execute($command);

        	$attributes = array();
		$types = array();
		while (!$this->eot())
		{
            		$attr = $this->fetchRow();
            		$attributes[] = $attr;
            		$t = $this->fetchRow();
			$types[] = $t;
		}
        	return array($attributes, $types);
	}

	/**
	* Creates a directory on amga
	* @access public
	* @param string $dir name of the directory we want to create
	* @return ret val (RetrieveResult)
	*/
	function createDir($dir)
	{
		$command = 'createdir ' . $dir;
        	return $this->execute($command);
	}

	/**
	* Removes a directory from amga
	* @access public
	* @param string $dir name of the directory we want to remove
	* @return 0 if OK or num_erro
	*/
	function removeDir($dir)
	{
		$command = 'rmdir ' . $dir;
        	return $this->execute($command);
	}

	/**
	* Removes a file from amga
	* @access public
	* @param string $path name of the file to remove
	* @return 0 if OK or num_erro
	*/
	function rm($path)
	{
		$command = 'rm ' . $path;
        	return $this->execute($command);
	}

	/**
	* Finds entries in the catalogue matching a query
	* Returns all files in the catalogue matching a given 
    * filename pattern and SQL-like query.
	* @access public
	* @param string $pattern the pattern matching possible filenames(entries)
	* @param string $query the SQL-Like query 
	* @return 0 if OK or num_erro
	*/
	function find($pattern, $query)
	{
		$command = 'find ';
        	$command = $command . ' ' . $pattern;
        	$command = $command . ' ' . $this->quoteValue($query);
        	return $this->execute($command);
	}

	/**
	* Returns given attributes of entries that match a SQL-like query.
	* @access public
	* @param array $attributes array of the attributes we want to get
	* @param string $query the SQL-Like query 
	* @return 0 if OK or num_erro
	*/
	function selectAttr($attributes, $query)
	{
		$command = 'selectattr ';
		foreach ($attributes as $i)
		{
			$command = $command . ' ' . $i;
		}
            
        	$this->nattrs = count($attributes);
		$command = $command . ' ' . $this->quoteValue($query);
		return $this->execute($command);
	}

	/**
	* Returns array of attributes
	* @access public
	* @return array of attributes
	*/
	function getSelectAttrEntry()
	{
		$attributes = array();
		for ($i = 0; $i < count($this->nattrs); $i++)
		{
			$attr = $this->fetchRow();
			$attributes[] = $attr;
		}
        	return $attributes;
	}

	/**
	* Returns given attributes of entries that match a SQL-like query.
	* @access public
	* @param array $attributes array of the attributes we want to get
	* @param string $query the SQL-Like query 
	* @return 0 if OK or num_erro
	*/
	function updateAttr($pattern, $updateExpr, $condition)
	{
		$command = 'updateattr ' . $pattern;
		foreach($updateExpr as $i)
		{
			list($var, $exp) = $this->splitUpdateClause($i);
            		$command = $command . ' ' . $var . ' ' . $this->quoteValue($exp);
		}
        	$command = $command . ' ' . $this->quoteValue($condition);
        	return $this->execute($command);
	}

	/**
	* Uploads a file to a given collection
	* @access public
	* @param string $collection name of the collection
	* @param array $attributes array of the attributes
	* @return 0 if OK or num_erro
	*/
	function upload($collection, $attributes)
	{
		$command = 'upload ' . $collection;
		foreach($attributes as $i)
		{
			$command = $command . ' ' . $i;
		}
		$this->nattrs = count($attributes);
		return $this->executeNoWait($command);
	}

	/**
	* Puts another entry into the collection during an upload()
	* This call sends data to the server after an upload has been
	* prepared with the upload() call
	* @access public
	* @param string $file name of the file
	* @param array $values array of values to set for the entry
	* @return 0 if OK or num_erro
	*/
	function put($file, $values)
	{
		$command = 'put ' . $file;
		if (count(values) != $this->nattrs)
		{
            		//throw new Exception("Illegal command");
            		$this->err_msg .= "Illegal command";
			return false;
		}
		foreach ($values as $i)
		{
			$command = $command . ' ' . $i;
		}
		return $this->executeNoWait($command);
	}

	/**
	* Aborts an upload
	* @access public
	* @return 0 if OK or num_erro
	*/
	function abort()
	{
		$command = 'abort';
		return $this->execute($command);
	}

	/**
	* Commits an upload
	* @access public
	* @return 0 if OK or num_erro
	*/
	function commit()
	{
		$command = 'commit';
		return $this->execute($command);
	}

	/**
	* Creates a sequence
	* @access public
	* @param string $name name of the sequence
	* @param string $directory name of the directory
	* @param string $increment step of the sequence 
	* @param string $start first value of the sequence 
	* @return 0 if OK or num_erro
	*/
	function sequenceCreate($name, $directory, $increment = 1, $start = 1)
	{
		$command = 'sequence_create ' . $name . " " . $directory . " ";
		$command = $command . $increment . " " . $start;
		return $this->execute($command);
	}

	/**
	* Returns next element in the sequence
	* @access public
	* @param string $name name of the sequence
	* @return $string next element in the sequence
	*/
	function sequenceNext($name)
	{
		$command = 'sequence_next ' . $name;
		return $this->execute($command);
		return $this->fetchRow();
	}

	/**
	* Removes a sequence
	* @access public
	* @param string $name name of the sequence
	* @return 0 if OK or num_erro
	*/
	function sequenceRemove($name)
	{
		$command = 'sequence_remove ' . $name;
		return $this->execute($command);
	}

	/**
	* Changes current directory
	* @access public
	* @param string $dir name of new directory
	* @return 0 if OK or num_erro
	*/
	function cd($dir)
	{
		$command = 'cd ' . $dir;
		return $this->execute($command);
	}

	/**
	* Function used to retrieve the login of the user
	* @access public
	* @return string the login name of the user connected, 
	* @return 0 if OK or num_erro string 201 Could not authenticate user 
	*/
	function whoami()
	{
		$command = 'whoami';
		$line=$this->execute($command);
		// yzz dixit
		//$line=$this->fetchRow();
		if ($this->debug)
			echo "Feched line: ".$line."<br>";
		return $line;
	}

	/**
	* Function used to retrieve the groups to which the user belongs
	* @access public
	* @return string comma separated list of groups
	*/
	function grp_member()
	{
		$command = 'grp_member';
		$this->execute($command);
		return $this->fetchRow();
	}
	
	/**
	* Function used to split the update clause in order
	* to build a valid command for updateAttr
	* @access public
	* @param string $clause the update clause to be split
	* @return void
	*/
	function splitUpdateClause($clause)
	{
		// skip leading white space
		$i = 0;
		while ($i < strlen($clause) && ($clause[i] == ' ' || $clause[$i] == '\t'))
		{
			$i++;
			$clause = substr($clause, $i);
		}
       
        	$escaped = false;
	        $quoted = false;

        	$i = 0;
	        while ($i < strlen($clause))
		{
       		     if ($clause[$i] == "'" && !$escaped)
				$quoted = !$quoted;
       		     if ($clause[$i] == '/')
       			         $escaped = !$escaped;
       		     if (($clause[$i] == ' ' || $clause[$i] == '/t') && !$quoted)
       		         break;
       		     $i++;
		}

       		if ($i == 0 || $i >= strlen($clause) - 1)
		{
			//throw new Exception("Invalid update statement");
			$this->err_msg .= "Invalid update statement<br>";
			return false;
		}
                            
		$var = substr($clause, 0, $i);
		$exp = substr($clause, $i+1);
		$i1 = strpos($exp, "'");
		$i2 = strrpos($exp, "'");
		if ($i1 == 0 && $i2 == strlen($exp)-1)
			$exp = substr($exp, $i1+1, $i2);
		$i1 = strpos($var, "'");
		$i2 = strrpos($var, "'");   
		if ($i1 == 0 && $i2 == strlen($var)-1)
			$var = substr($var, $i1+1, $i2);

		return array($var, $exp);
	}

	/**
	* Function used to retrieve the greetings message
	* @access public
	* @return string the greetings message
	*/
	function getGreetings()
	{
		return $this->greetings;
	}

	/**
	* Function used to retrieve the number of attributes used
	* @access public
	* @return string the number of attributes set by the previous command
	*/
	function getNAttrs()
	{
		return $this->nattrs;
	}

	/**
	* Function used to retrieve the protocol version
	* @access public
	* @return string the protocol version
	*/
	function getProtocolVersion()
	{
		return $this->protocolVersion;
	}

	/**
	* Functions to retrieve the user home directory
	* application may initialice with set_home_directory before this.
	* @access public
	* @return string 
	*/
	function getHome_directory()
	{
		return $this->home_directory;
	}

	/**
	* Function to assign the user home directory
	* @access public
	* @param string $home_directory the update clause to be split
	* @return string
	*/
	function setHome_directory($home_directory)
	{
		$this->home_directory=$home_directory; 
	}

	/**
	* Function used to retrieve the protocol version
	* @access public
	* @return string the protocol version
	*/
	function getErr_msg()
	{
		return $this->err_msg;
	}

	/**
	* Public access o sessionID.
	* @access public
	* @return void
	*/
	function setSessionID($sessionid)
	{
		$this->sessionID = $sessionid;
	}

	/**
	* Public access o sessionID.
	* @access public
	* @return sessionID
	*/
	function getSessionID()
	{
		return $this->sessionID;
	}

	/**
	* Function used to set the debug mode on/off
	* @access public
	* @return void
	*/
	function setDebugMode($mode = false)
	{
		$this->debug = $mode;
	}

	/**
	* Function used to get the filename of an absoulte path
	* @access public
	* @return name of path
	*/
	function saca_name_of_path($path)
	{
		$rev_path=strrev($path);
		$pos=strpos($rev_path,"/");

		if (!$pos)
			return $path;

		$rev_result=substr($rev_path,0,$pos);
		$result=strrev($rev_result);

		return $result;
	}

	/**
	* Function used pop tokens of a string
	* @access public
	* @return token
	*/
	function saca_token_buffer($separador)
	{
		$pos=strpos($this->buffer,$separador);
		$result=substr($this->buffer,0,$pos);
		$lentotal=strlen($this->buffer);
		$this->buffer=substr($this->buffer,$pos,$lentotal-$pos);
		
		return $result;
	}
}
?>
