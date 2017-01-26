<?php
class Ssh{
	private $session;
	public $out;
	public $err;
	//construtor default
	function __construct($host, $porta, $user, $senha){
		$this->session = ssh2_connect($host, $porta);
		ssh2_auth_password($this->session, $user, $senha);
	}

	function exec($comando){
		if ($stream = ssh2_exec($this->session, $comando)){
			$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

			// Enable blocking for both streams
			stream_set_timeout($errorStream, 600);
			stream_set_timeout($stream, 600);
			stream_set_blocking($errorStream, true);
			stream_set_blocking($stream, true);

			// Whichever of the two below commands is listed first will receive its appropriate output.  The second command receives nothing
			$this->out = trim( stream_get_contents( $stream ) );
      $this->err = trim( stream_get_contents( $errorStream ) );

        	// Close the streams
			fclose($errorStream);
			fclose($stream);

        	if($this->err == ""){
        		return true;
        	} else {
        		return false;
        	}
		}else{
			$this->out = "Falhou ao executar comando";
			$this->err = "Falhou ao executar comando";
			return false;
		}
	}
}
?>
