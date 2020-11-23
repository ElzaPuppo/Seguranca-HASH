<?php


	$metodo = $_POST['metodo'];
	$entrada = $_POST['entrada'];
	$chave = $_POST['passphrase'];
	$item = $_POST['item'];
	
	$entrada = file_get_contents('teste.txt');
	
	
	if ($metodo == "SHA"){
		$hash = hash('sha512', $entrada);	
	} else $hash = hash_hmac ('sha512', $entrada, $chave);
	
	
	$Cifra =  'AES-256-CBC';
	$VI = openssl_random_pseudo_bytes(openssl_cipher_iv_length($Cifra));
	
	if ($item == 'A'){
		
		$middle = $entrada.':'.$hash;
		
		$TextoCriptografado = openssl_encrypt($middle, $Cifra, $chave, OPENSSL_RAW_DATA ,$VI);
		$TextoCriptografado = base64_encode($TextoCriptografado);
		$VI = base64_encode($VI);
		$CriptoExibir = $TextoCriptografado.':'.$VI;
		$textoFinal = $CriptoExibir;
		//inverso
		$Resultado = explode(':', $CriptoExibir);
		$VI = base64_decode($Resultado[1]);
		$TextoCriptografado = base64_decode($Resultado[0]);
		$DescriptoExibir = openssl_decrypt($TextoCriptografado, $Cifra, $chave, OPENSSL_RAW_DATA , $VI);
		$Palavra = explode(':', $DescriptoExibir);
		$saida = $Palavra[0];
		$NewHash = $Palavra[1];
		echo '<br><div style="width: 90%; word-wrap: break-word;" ><center><h3><br> Hash:<b> ' .$hash.'</b><br><br>Arquivo com o hash: <b>' .$middle.' </b><br><br> Arquivo criptografada: <b>' .$CriptoExibir. '</b><br><br> Palavra descriptografada: <b>'.$DescriptoExibir.  '</b><h3></center><br><br></div>';
		
		
	} else {
		if ($item == 'B'){			
			
			$TextoCriptografado = openssl_encrypt($hash, $Cifra, $chave, OPENSSL_RAW_DATA ,$VI);
			$TextoCriptografado = base64_encode($TextoCriptografado);
			$VI = base64_encode($VI);
			$CriptoExibir = $TextoCriptografado.':'.$VI;
			$middle = $entrada.':'.$CriptoExibir;
			//inverso
			$palavra = explode(':', $middle);
			$saida = $palavra[0];
			$Resultado = base64_decode($palavra[1]);
			$VI = base64_decode($palavra[2]);
			$NewHash = openssl_decrypt($Resultado, $Cifra, $chave, OPENSSL_RAW_DATA , $VI);
			$final = $saida.':'.$NewHash;
			echo '<br><div style="width: 90%; word-wrap: break-word;" ><center><h3><br> Hash:<b> ' .$hash.'</b><br><br>Palavra com Hash Criptografado: <b>' .$middle.' </b><br><br> Palavra com hash descriptografado: <b>'.$final.  '</b><br><h3></center><br><br></div>';
		
		}
	
		else {
			$publickey = file_get_contents('public_key.pem');
			openssl_public_encrypt($hash, $crypttext, $publickey);
			$middle= $entrada.':'.base64_encode($crypttext);
			//inverso
			$privkey = file_get_contents('private_key.pem');
			$priv = openssl_pkey_get_private($privkey, $chave);
			$palavra = explode(':', $middle);
			$saida = $palavra[0];
			openssl_private_decrypt(base64_decode($palavra[1]), $NewHash, $priv);
			$final = $saida.':'.$NewHash;
			echo '<br><div style="width: 90%; word-wrap: break-word;" ><center><h3><br> Hash:<b> ' .$hash.'</b><br><br>Palavra com Hash Criptografado: <b>' .$middle.' </b><br><br> Palavra com hash descriptografado: <b>'.$final.  '</b><br><h3></center><br><br></div>';
		
		}
	}
		
		if ($metodo == "SHA"){
			$hash = hash('sha512', $saida);	
		} else $hash = hash_hmac ('sha512', $saida, $chave);
		
		
		if ($hash == $NewHash){
			$sucesso = 'compatível';			
		} else $sucesso = 'não é compatível';
		
		echo '<br><div style="width: 90%; word-wrap: break-word;" ><center><h3><br> <br> Verificação se o hash é o mesmo: <b>'.$sucesso. '</b><h3></center><br><br></div>';
		

?>

