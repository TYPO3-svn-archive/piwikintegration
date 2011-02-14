<?php
class Piwik_KSVisitorImport_Import_ApacheExtended extends Piwik_KSVisitorImport_Import_Abstract {
	function lineHandler($line) {
		//explode line
		preg_match('/(\S+) (\S+) (\S+) \[(([^:]+):((\d+):(\d+):(\d+)) ([^\]]+))\] "(\S+) (.+?) (\S+)" (\S+) (\S+) "([^"]+)" "([^"]+)"/', $line, $matches);
		if(is_array($matches) && array_key_exists(0, $matches)) {
			$result = array('fullString'    => $matches[0],
					   	    'remoteHost'    => $matches[1],
						    'identUser'     => $matches[2],
						    'authUser'      => $matches[3],
						    'unixTimestamp' => strtotime($matches[4]),
						    'fullDateTime'  => $matches[4],
						    'date'          => $matches[5],
						    'time'          => $matches[6],
						    'h'             => $matches[7],
						    'm'             => $matches[8],
						    's'             => $matches[9],
						    'timezone'      => $matches[10],
						    'method'        => $matches[11],
						    'url'           => $matches[12],
						    'protocol'      => $matches[13],
						    'status'        => $matches[14],
						    'bytes'         => $matches[15],
						    'referrer'      => $matches[16],
						    'userAgent'     => $matches[17],
						    'siteName'      => preg_replace('/[\/\.]+/', ' ', $matches[12]),
						    'idsite'        => $this->idSite,
				);
			$this->makeEntry(
				$result
			);
			return true;
		}
	}
}

?>