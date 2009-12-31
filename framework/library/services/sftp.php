<?php
namespace library\services; //has to match namespace reference in client code
/**
 * SFTP - access to an SFTP server.
 * Mimics David Grundl's FTP - http://code.google.com/p/ftp-php/ 
 * author: TQ White II
 *
 */
class Sftp
{
	private $host;
	private $port;
	
	private $sshHandle;
	private $ftpHandle;
	
	public function __construct(){
		echo "<div style=color:red;font-size:8pt;>Sftp::put says, mode is ignored, always 0644</div>";

	}
	
	public function connect($host, $port){
	$this->host=$host;
	$this->port=$port;
	}
	
	
	public function pasv($flag){
		//for compatibility with ftp class
		return true;
	}
	
	public function login($user, $password){
	
		$this->sshHandle=ssh2_connect($this->host, $this->port);
		$authResult=ssh2_auth_password($this->sshHandle, $user, $password);
		if ($authResult){
			$this->ftpHandle=ssh2_sftp($this->sshHandle);
		}
		
		return $authResult;
	
	}
	
	public function mkDirRecursive($dirPath){
		$result=ssh2_sftp_mkdir($this->ftpHandle, $dirPath, 0777, true); //true refers to 'recursive'
		
		return $result;
	}
	
	public function put($destPath, $localPath, $mode=''){
		$result=ssh2_scp_send($this->sshHandle, $localPath, $destPath, 0644);
		
		return $result;
	}
	
	public function delete ($remoteFilePath){
		$result=ssh2_sftp_unlink($this->ftpHandle, $remoteFilePath);
		return $result;
	}
	
	public function close(){
	//for compatibility with ftp
	return true;
	}
	

} //end of class

/*

$ftp = new Ftp;
			$ftp->		connect($this->ftpHost);
			$ftp->		login($this->ftpUser, $this->ftpPass);
			$ftp->		pasv(true);
	$result=$ftp->		mkDirRecursive(dirname($destPath));
	$result=$ftp->		put('./'.$destPath, $localPath, FTP_BINARY);
			$ftp->		close();
			
			$ftp->		connect($this->ftpHost);
			$ftp->		login($this->ftpUser, $this->ftpPass);
	$result=$ftp->		delete('./'.$destPath);
			$ftp->		close();
			*/