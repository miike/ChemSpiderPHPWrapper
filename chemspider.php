<?php
//this is a loose wrapper for ChemSpider API conversion utilities
//github.com/miike
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$token = ""; //your chemspider API token goes here (required for some functions)

class Conversion
{
	function handler($url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);

		//check if we can reach the service (data is returned)
		if (!($data)){
			echo "Failed to connect to Chemspider";
			return -1;
		}

		$xml = simplexml_load_string($data);
		
		//check that the XML parsed successfully
		if (!($xml)){
			echo "Failed to parse Chemspider XML\n\n";
			print_r($data);
			return -1;
		}
		
		//if we're clear return the parsed xml
		return $xml;
	}

	function parser($xml){
		//just selects the first element of XML
		if ($xml != -1){
			return (string)$xml[0];
		}
		else{
			echo "An error occurred. See above.";
		}
	}

	function csid2mol($csid, $token){ //$csid (string), $token (string) - required, returns string
		$csid = urlencode($csid);
		$url = "http://chemspider.com/InChI.asmx/CSIDToMol?csid=$csid&token=$token";
		$csid = urlencode($csid);
		
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function inchikey2csid($inchi){ //$inchi (string), returns string
		//token not required
		$inchi = urlencode($inchi);
		$url = "http://chemspider.com/InChI.asmx/InChIKeyToCSID?inchi_key=$inchi";
		$xml = $this->handler($url);
		return $this->parser($xml);
		}


	function inchi2csid($inchi){ //$inchi (string), returns string
		//token not required
		$inchi = urlencode($inchi);
		$url = "http://chemspider.com/InChI.asmx/InChIToCSID?inchi=$inchi";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function inchi2smiles($inchi){ //$inchi (string), returns string
		$inchi = urlencode($inchi);
		$url = "http://chemspider.com/InChI.asmx/InChIToSMILES?inchi=$inchi";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function smiles2inchi($smiles){ //$smiles (string), returns string
		$smiles = urlencode($smiles);
		$url = "http://chemspider.com/InChI.asmx/SMILESToInChI?smiles=$smiles";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function inchi2inchikey($inchi){
		$inchi = urlencode($inchi);
		$url = "http://chemspider.com/InChI.asmx/InChIToInChIKey?inchi=$inchi";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function inchi2mol($inchi){
		$inchi = urlencode($inchi);
		$url = "http://chemspider.com/InChI.asmx/InChIToMol?inchi=$inchi";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function mol2inchi($mol){
		$mol = urlencode($mol);
		$url = "http://chemspider.com/InChI.asmx/MolToInChI?mol=$mol";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function mol2inchikey($mol){
		$mol = urlencode($mol);
		$url = "http://chemspider.com/InChI.asmx/MolToInChIKey?mol=$mol";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}

	function inchikey2mol($inchikey){
		$inchikey = urlencode(inchikey);
		$url = "http://chemspider.com/InChI.asmx/InChIKeyToMol?inchi_key=$inchikey";
		$xml = $this->handler($url);
		return $this->parser($xml);
	}
	
	function openbabel($structure, $from, $to){
		$what = urlencode($structure);
		$from = urlencode($from);
		$to = urlencode($to);
		
		$url = "http://www.chemspider.com/OpenBabel.asmx/Convert?what=$what&fromFormat=$from&toFormat=$to";
		$xml = $this->handler($url);
		return $this->parser($xml);
	
	}
	
	function convert($structure, $toformat, $fromformat){
		//$structure - input structure (string)
		//$toformat - input structure format (string)
		//$fromformat - output structure format (string)
	
		$concatformat = $toformat . "-" . $fromformat;
		//could be completed with $func, but that looks a little messy.
		//although this probably isn't the best/neatest way to do this.
		switch ($concatformat){
			case "csid-mol":
				return $this->csid2mol($structure);
				break;
			case "inchikey-csid":
				return $this->inchikey2csid($structure);
				break;
			case "inchi-csid":
				return $this->inchi2csid($structure);
				break;
			case "inchi-smiles":
				return $this->inchi2smiles($structure);
				break;
			case "smiles-inchi":
				return $this->smiles2inchi($structure);
				break;
			case "inchi-inchikey":
				return $this->inchi2inchikey($structure);
				break;
			case "inchi-mol":
				return $this->inchi2mol($structure);
				break;
			case "mol-inchi":
				return $this->mol2inchi($structure);
				break;
			case "mol-inchikey":
				return $this->mol2inchikey($structure);
				break;
			case "inchikey-mol":
				return $this->inchikey2mol($structure);
				break;
			default;
				return 'A function does not exist to perform this conversion.' . $concatformat;
		}
	}
}

class Search
{
	function handler($url){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		if (!($data)){
			echo "Failed to connect to Chemspider";
			return -1;
		}
		$xml = simplexml_load_string($data);
		
		if (!($xml)){
			//echo "Failed to parse Chemspider XML\n\n";
			print_r($data);
			return -1;
		}
		return $xml;
	}
	
	function simple($searchterm, $token){ //takes a search argument, returns CSID
		$term = urlencode($searchterm);
		$url = "http://www.chemspider.com/Search.asmx/SimpleSearch?query=$term&token=$token";
		//echo $url;
		$xml = $this->handler($url);
		if ($xml != -1){
			$csid = $xml->int;
			return $csid;
		}
		else{
			return -1;
		}
	}
	
	function compoundInfo($csid, $token){
		$url = "http://www.chemspider.com/Search.asmx/GetCompoundInfo?CSID=$csid&token=$token";
		$xml = $this->handler($url);
		
		if ($xml != -1){
			$compound = array();
			$compound['csid'] = (string)$xml->CSID;
			$compound['inchi'] = (string)$xml->InChI;
			$compound['inchikey'] = (string)$xml->InChIKey;
			$compound['smiles'] = (string)$xml->SMILES;
			return $compound;
		}
		else{
			return -1;
		}
	}
	
	function extendedCompoundInfo($csid, $token){
		$url = "http://www.chemspider.com/MassSpecAPI.asmx/GetExtendedCompoundInfo?CSID=$csid&token=$token";
		$xml = $this->handler($url);
		
		if ($xml != -1){
			$compound = array();
			$compound['csid'] = (string)$xml->CSID;
			$compound['inchi'] = (string)$xml->InChI;
			$compound['inchikey'] = (string)$xml->InChIKey;
			$compound['smiles'] = (string)$xml->SMILES;
			$compoound['mf'] = (string)$xml->MF;
			$compound['avgmass'] = (string)$xml->AverageMass;
			$compound['mw'] = (string)$xml->MolecularWeight;
			$compound['mm'] = (string)$xml->MonoisotopicMass;
			$compound['nm'] = (string)$xml->NominalMass;
			$compound['alogp'] = (float)$xml->ALogP;
			$compound['xlogp'] = (float)$xml->XLogP;
			$compound['common'] = (string)$xml->CommonName;
			return $compound;
		}
		else{
			return -1;
		}
	
	
	}


}

//conversion list (function names)
//csid2mol, inchikey2csid, inchi2csid, inchi2smiles, smiles2inchi, 
//inchi2inchikey, inchi2mol, mol2inchi, mol2inchikey, inchikey2mol

//EXAMPLES BELOW

//CONVERSION EXAMPLES

$converter = new Conversion();
//echo $converter->convert("[Na+].[Cl-]", "smiles", "inchi");
//echo $converter->convert("InChI=1S/H2O/h1H2", "inchi", "csid");
//echo $converter->convert("FAPWRFPIFSIZLT-UHFFFAOYSA-M", "inchikey", "csid");
//echo $converter->convert("InChI=1S/H2O/h1H2", "inchi", "smiles");
//echo $converter->openbabel("InChI=1S/H2O/h1H2", "inchi", "png");

//SEARCH EXAMPLES
$searcher = new Search();
//print_r($searcher->compoundInfo(5044, $token));
//print_r($searcher->extendedCompoundInfo(5044, $token));


//determine if it's an ajax request
if ($_POST){
	//we don't sanitise input here yet as we presume it's safe (for non-production environments)
	$input = $_POST['structure'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	echo $converter->convert($input, $from, $to);
}


?>