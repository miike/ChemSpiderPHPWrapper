<?php

$token = ""; //your chemspider API token goes here (required for some functions)

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
	
	$xml = handler($url);
	return parser($xml);
}

function inchikey2csid($inchi){ //$inchi (string), returns string
	//token not required
	$inchi = urlencode($inchi);
	$url = "http://chemspider.com/InChI.asmx/InChIKeyToCSID?inchi_key=$inchi";
	$xml = handler($url);
	return parser($xml);
	}


function inchi2csid($inchi){ //$inchi (string), returns string
	//token not required
	$inchi = urlencode($inchi);
	$url = "http://chemspider.com/InChI.asmx/InChIToCSID?inchi=$inchi";
	$xml = handler($url);
	return parser($xml);
}

function inchi2smiles($inchi){ //$inchi (string), returns string
	$inchi = urlencode($inchi);
	$url = "http://chemspider.com/InChI.asmx/InChIToSMILES?inchi=$inchi";
	$xml = handler($url);
	return parser($xml);
}

function smiles2inchi($smiles){ //$smiles (string), returns string
	$smiles = urlencode($smiles);
	$url = "http://chemspider.com/InChI.asmx/SMILESToInChI?smiles=$smiles";
	$xml = handler($url);
	return parser($xml);
}

function inchi2inchikey($inchi){
	$inchi = urlencode($inchi);
	$url = "http://chemspider.com/InChI.asmx/InChIToInChIKey?inchi=$inchi";
	$xml = handler($url);
	return parser($xml);
}

function inchi2mol($inchi){
	$inchi = urlencode($inchi);
	$url = "http://chemspider.com/InChI.asmx/InChIToMol?inchi=$inchi";
	$xml = handler($url);
	return parser($xml);
}

function mol2inchi($mol){
	$mol = urlencode($mol);
	$url = "http://chemspider.com/InChI.asmx/MolToInChI?mol=$mol";
	$xml = handler($url);
	return parser($xml);
}

function mol2inchikey($mol){
	$mol = urlencode($mol);
	$url = "http://chemspider.com/InChI.asmx/MolToInChIKey?mol=$mol";
	$xml = handler($url);
	return parser($xml);
}

function inchikey2mol($inchikey){
	$inchikey = urlencode(inchikey);
	$url = "http://chemspider.com/InChI.asmx/InChIKeyToMol?inchi_key=$inchikey";
	$xml = handler($url);
	return parser($xml);
}

//conversion list (function names)
//csid2mol, inchikey2csid, inchi2csid, inchi2smiles, smiles2inchi, 
//inchi2inchikey, inchi2mol, mol2inchi, mol2inchikey, inchikey2mol

//future implement openbabels 'convert' function

//EXAMPLES BELOW
//echo csid2mol('1', $token);
//echo inchi2csid('InChI=1S/H2O/h1H2');
//echo inchikey2csid('FAPWRFPIFSIZLT-UHFFFAOYSA-M');
//echo inchi2smiles('InChI=1S/H2O/h1H2');
echo smiles2inchi('[Na+].[Cl-]');

?>