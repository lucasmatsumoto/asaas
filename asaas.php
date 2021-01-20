<?php
class asaas{
	
	//Variaveis protegidas_________________________
	private $host = '127.0.0.1';
	private $user = 'usuario';
	private $pass = 'senha';
	private $banco = 'asaas';
	
	private $codcliente;
	private $cliente;
	private $urlTeste;
	private $urlProducao;
	private $apiKey;
	
	private $url;
	private $apikey;
	private $ambiente;
	private $conexao;
	//_____________________________________________
		
	//Getters______________________________________
	
	private function GetCodCliente($codcliente){
		return $this->codcliente;
	}
	
	private function GetCliente(){
		return $this->cliente;
	}
	
	private function GetUrlTeste(){
		return $this->urlTeste;
	}
	
	private function GetUrlProducao(){ 
		return $this->urlProducao;
	}
	
	private function GetApiKey(){
		return $this->apiKey;
	}
	
	private function GetHost(){
		return $this->host;
	}
	
	private function GetUser(){
		return $this->user;
	}
	
	private function GetPass(){
		return $this->pass;
	}
	
	private function GetBanco(){
		return $this->banco;
	}
	
	private function GetUrl(){
		return $this->url;
	}
	
	private function GetAmbiente(){
		return $this->ambiente;
	}
	//_____________________________________________
	
	//Setters______________________________________
	
	private function SetCodCliente($codcliente){
		$this->codcliente = $codcliente;
	}
	
	private function SetCliente($cliente){
		$this->cliente = $cliente;
	}
	
	private function SetUrlTeste($urlTeste){
		$this->urlTeste = $urlTeste;
	}
	
	private function SetUrlProducao($urlProducao){
		$this->urlProducao = $urlProducao;
	}
	
	private function SetApiKey($apiKey){
		$this->apiKey = $apiKey;
	}
	
	private function SetUrl($url){
		$this->url = $url;
	}
	
	private function SetAmbiente($ambiente){
		$this->ambiente = $ambiente;
		
		if($ambiente=='T'){
			$this->SetUrl($this->GetUrlTeste());
		}elseif($ambiente=='P'){
			$this->SetUrl($this->GetUrlProducao());
		}
	}
	
	//Faz a conexão com o banco de dados e define as variaveis padrão do ASAAS
	private function SetConexao($cliente,$dados){
		
		//Verifica se vai usar a conexão padrão ou se o usuário informou uma conexão
		if(empty($dados)){
			$host = $this->GetHost();
			$user = $this->GetUser();
			$pass = $this->GetPass();
			$banco = $this->GetBanco();
		}
		else{
			$host = $dados[0];
			$user = $dados[1];
			$pass = $dados[2];
			$banco = $dados[3];
		}		 
		
		//Cria a conexão
		$con=mysqli_connect($host,$user,$pass,$banco);
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		//Busca os dados padrão do ASAAS
		$resultado = mysqli_query($con,"SELECT CodCliente, Cliente, UrlTeste, UrlProducao, ApiKey FROM tblconfig WHERE CodCliente = '$cliente'");	
		
		$row=mysqli_fetch_array($resultado,MYSQLI_ASSOC);
		
		//Define as variaveis padrão
		$this->SetCodCliente($row['CodCliente']);
		$this->SetCliente($row['Cliente']);
		$this->SetUrlTeste($row['UrlTeste']);
		$this->SetUrlProducao($row['UrlProducao']);
		$this->SetApiKey($row['ApiKey']);
		
		//Retorna o ApiKey do ASAAS
		return $this->GetApiKey();
	}
	//_____________________________________________
	
	
	//Funções______________________________________
	 
	//Ao Construir a class define a conesão e o ambiente (T=Teste, P=Producao)
	function __construct($cliente,$ambiente,$dados=''){
		$this->SetConexao($cliente,$dados);
		$this->SetAmbiente($ambiente);
	}
	
	//METODOS DE REQUISIÇÃO DO ASAAS
	
	//DELETE
	private function DeleteAsaas($url){
		//Pega o ApiKey
		$apiKey = $this->GetApiKey();
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL =>  $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
			"access_token: $apiKey"
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		//Retorna os dados ou os erros
		if ($err) {
			return json_decode($err,true);
		} else {
			return json_decode($response,true);
		}
	}

	//GET
	private function GetAsaas($url){
		//Pega o ApiKey
		$apiKey = $this->GetApiKey();
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"access_token: $apiKey",
		  ),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		//Retorna os dados ou os erros
		if ($err) {
			return json_decode($err,true);
		} else {
			return json_decode($response,true);
		}
	}
	
	//POST
	private function PostAsaas($url, $dados){
		//Pega o ApiKey
		$apiKey = $this->GetApiKey();
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => http_build_query($dados),
		  CURLOPT_HTTPHEADER => array(
			"access_token: $apiKey",
		  ),
		));
				
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		//Retorna os dados ou os erros
		if ($err) {
			return json_decode($err,true);
		} else {
			return json_decode($response,true);
		}
	}
	
	//CLIENTES________________________________________________________________________
	
	//Cadastra um cliente
	function ClienteNovo($NomeAsas,$EmailAsas,$CpfASas,$CepASas,$TelefoneASAAS,$EnderecoASas,$NumeroCasaAsas,$ComplementoAsas,$CodigoRefAsas)
	{
		$url = $this->GetUrl().'/api/v3/customers';
				
		$dados = array('name'=>$NomeAsas,
						'email'=>$EmailAsas,
						'cpfCnpj'=>$CpfASas,
						'postalCode'=>$CepASas,
						'mobilePhone'=>$TelefoneASAAS,
						'address'=>$EnderecoASas,
						'addressNumber'=>$NumeroCasaAsas,
						'complement'=>$ComplementoAsas,
						'externalReference'=>$CodigoRefAsas,
						'notificationDisabled'=>true);
		
		return $this->PostAsaas($url, $dados);
	}
	
	//Consulta um cliente
	function Cliente($customer){
		$url = $this->GetUrl().'/api/v3/customers/'.$customer;
		
		return $this->GetAsaas($url);
		
	}
	
	//Busca uma lista de clientes por filtro
	function ClientesListar($dados)
	{
		/*A Variavel dados receber um array com os seguintes indices name, email, cpfcnpj, externalReference, offset, limit
		Obs: offset é o numero da pagina no asaas
		O Array pode conter 1 ou mais desses parametros, não é obrigatório preencher todos
		*/
		
		$valores = '';
		
		if(!empty($dados['name'])){
			$valores .= 'name='.$dados['name'].'&';
		}
		
		if(!empty($dados['email'])){
			$valores .= 'email='.$dados['email'].'&';
		}
		
		if(!empty($dados['cpfCnpj'])){
			$valores .= 'cpfCnpj='.$dados['cpfCnpj'].'&';
		}
		
		if(!empty($dados['externalReference'])){
			$valores .= 'externalReference='.$dados['externalReference'].'&';
		}
		
		if(!empty($dados['offset'])){
			$valores .= 'offset='.$dados['offset'].'&';
		}
		
		if(!empty($dados['limit'])){
			$valores .= 'limit='.$dados['limit'].'&';
		}else{
			$valores .= 'limit=100&';
		}
		
		$valores = substr($valores,0,-1);
		 
		if(!empty($valores)){
			$url = $this->GetUrl().'/api/v3/customers?'.$valores;
			return $this->GetAsaas($url);
		}
		
	}
	
	//Atualiza os dados do cliente
	function ClienteAtualizar($customer,$dados){		
		/*A Variavel dados receber um array com os seguintes indices name, cpfCnpj, email, phone, mobilePhone, address, addressNumber, complement,
		postalCode, externalReference, notificationDisabled, additionalEmails, municipalInscription, stateInscription
		O Array pode conter 1 ou mais desses parametros, não é obrigatório preencher todos
		*/
		$url = $this->GetUrl().'/api/v3/customers/'.$customer;
		$apiKey = $this->GetApiKey();
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
		

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Content-Type: application/json",
		  "access_token: $apiKey"
		));

		$response = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($response);
	}
	
	//Deleta um cliente
	function ClienteDeletar($customer){
		$url = $this->GetUrl().'/api/v3/customers/'.$customer;
		return $this->DeleteAsaas($url);
	}
	//______________________________________________
	 
	
	//COBRANÇA______________________________________
	
	
	 ///////////////////////___________CARTÃO DE CRÉDITO
	 
	 //Atualiza os dados do cliente
	function CartaoCriar($dados){		
		/*A Variavel dados receber um array com os seguintes indices name, cpfCnpj, email, phone, mobilePhone, address, addressNumber, complement,
		postalCode, externalReference, notificationDisabled, additionalEmails, municipalInscription, stateInscription
		O Array pode conter 1 ou mais desses parametros, não é obrigatório preencher todos
		*/
		$url = $this->GetUrl().'/api/v3/payments';
		$apiKey = $this->GetApiKey();
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
		

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Content-Type: application/json",
		  "access_token: $apiKey"
		));

		$response = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($response);
	}
	
	//Cria uma cobrança do tipo boleto no ASAAS
	
	function BoletoCriar($Customer,$Vencimento,$Valor,$Ref,$Parcelas='',$nomeevento){
		$url = $this->GetUrl().'/api/v3/payments';
		
		$apiKey = $this->GetApiKey();
		
		$dados = array(
					"customer" => $Customer,
					"billingType"=> "BOLETO",
					"dueDate"=> $Vencimento,
					"value"=> $Valor,
					"description"=> $nomeevento,
					"externalReference"=> "$Ref"
					);
		
		if(!empty($Parcelas) && $Parcelas > 1){
			unset($dados['value']);
			$dados['installmentCount'] = $Parcelas;
			$dados['installmentValue'] = $Valor;
		}
		/*
		///TIRAR O COMENTÁRIO CASO USE DESCONTO
		if(!empty($valdesc) && $valdesc > 1)
		{
			$dados['discount']['value']=$valdesc;
			$dados['discount']['type']="FIXED";
			$dados['discount']['dueDateLimitDays']=$datadesc;
		}
		*/
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
		

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Content-Type: application/json",
		  "access_token: $apiKey"
		));

		$response = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($response);

	}
	
	//Pega os dados de um boleto no ASAAS
	function Boleto($payment){
		$url = $this->GetUrl().'/api/v3/payments/'.$payment;
		return $this->GetAsaas($url);
	}
	
	//Busca uma lista de boletos do ASAAS com filtro
	function BoletoListar($dados, $limite_registro){
		
		/*A Variavel dados receber um array com os seguintes indices customer, billingType, status, subscription, installment, 
			externalReference, offset, limit
		Obs: offset é o numero da pagina no asaas
		O Array pode conter 1 ou mais desses parametros, não é obrigatório preencher todos
		*/
		
		$valores = '';
		
		if(!empty($dados['customer'])){
			$valores .= 'customer='.$dados['customer'].'&';
		}
		
		if(!empty($dados['billingType'])){
			$valores .= 'billingType='.$dados['billingType'].'&';
		}
		
		if(!empty($dados['status'])){
			$valores .= 'status='.$dados['status'].'&';
		}
		
		if(!empty($dados['subscription'])){
			$valores .= 'subscription='.$dados['subscription'].'&';
		}
		
		if(!empty($dados['installment'])){
			$valores .= 'installment='.$dados['installment'].'&';
		}
		
		if(!empty($dados['externalReference'])){
			$valores .= 'externalReference='.$dados['externalReference'].'&';
		}
		
		if(!empty($dados['paymentDate'])){
			$valores .= 'paymentDate='.$dados['paymentDate'].'&';
		}
		
		if(!empty($dados['offset'])){
			$valores .= 'offset='.$dados['offset'].'&';
		}
		
		/*
		if(!empty($dados['limit'])){
			$valores .= 'limit='.$dados['limit'].'&';
		}else{
			$valores .= 'limit=3&';
		}*/
		if(!empty($limite_registro))
		{
			$valores .= 'limit='.$limite_registro.'&';
		}
		else
		{
			$valores .= 'limit=3&';
		}
		
		$valores = substr($valores,0,-1);
		
		if(!empty($valores)){
			$url = $this->GetUrl().'/api/v3/payments?'.$valores.'';
			return $this->GetAsaas($url);
		}
	}
	
	//Listar um parcelamento de boletos - Retorna um array já com o indice da parcela
	function BoletoParcelas($installment){	
		/*O Asaas normalmente traz as parcelas em ordem decrescente porem ele pode mudar essa lógica e 
			ele nao traz o numero da parcela em um parametro apenas na descrição o que pode ser alterado
			a qualquer momento pelo Asaas.
			
			Para retornarmos um array em ordem vamos usar a seguinte logica
			1 - Retornar as parcelas do Asaas
			2 - Reescrever um array colocando como indice a data de vencimento sem traço
			3 - Usar a função do php ksort para ordernar um array pela chave - assim vai ficar em ordem as parcelas
			4 - Reescrever novamente o array que agora esta em ordem e colocar como indice o numero da parcela
				4.1 - Enquanto estiver reescrevendo o array vamos adicionar os parametros Numero da Parcela e Total de Parcelas
		*/
		
		//1º Passo
		$url = $this->GetUrl().'/api/v3/payments?installment='.$installment.'&limit=50';
		$parcelas = $this->GetAsaas($url); //Pega as parcelas
		
		//2º Passo
		$arrParcelas = array();
		foreach($parcelas['data'] as $valores){
			$data = str_replace('-','',$valores['originalDueDate']);
			$arrParcelas[$data] = $valores;
		}
		
		//3º Passo
		ksort($arrParcelas);
		
		//4º Passo
		$vParcelas = array();
		$i=1;
		
		$qtd = count($arrParcelas); //Conta a quantidade de parcelas
		
		foreach($arrParcelas as $ordem)
		{
			$vParcelas[$i] = $ordem; //Inclui o array com as informações da parcela
			
			//4.1º Passo - Inclui as informações de Numero de Parcela e Quantidade de parcelas
			$vParcelas[$i]['NumeroParcela'] = $i; //Informa o numero da parcelas
			$vParcelas[$i]['TotalParcelas'] = $qtd; //Informa o total de parcelas
			
			$i++; //Passa para a proxima parcela
		}
		return $vParcelas;
		
	}
	
	//Atualiza os dados do boleto no ASAAS
	function BoletoAtualizar($id,$valores){
		/*A Variavel dados receber um array com os seguintes indices dueDate, value, description, externalReference
		O Array pode conter 1 ou mais desses parametros, não é obrigatório preencher todos
		*/
				
		if(!empty($valores['dueDate'])){
			$dados['dueDate'] = $valores['dueDate'];
		}
		
		if(!empty($valores['value'])){
			$dados['value'] = $valores['value'];
		}

		if(!empty($valores['description'])){
			$dados['description'] = $valores['description'];
		}

		if(!empty($valores['externalReference'])){
			$dados['externalReference'] = $valores['externalReference'];
		}
			
		//Verifica se o id é uma string para alterar um boleto ou um array para alterar em lote
		if(!is_array($id)){
			$url = $this->GetUrl().'/api/v3/payments/'.$id;
			return $this->PostAsaas($url,$dados);
		}
		else{
			$boletos = array();
			foreach($id as $fr){
				$url = $this->GetUrl().'/api/v3/payments/'.$fr;
				$boletos[] = $this->PostAsaas($url,$dados);
			}
			return $boletos;
		}
	}
	
	//Deleta um boleto do ASAAS
	function BoletoDeletar($id){
		//Verifica se o id é uma string para deletar um boleto ou um array para deletar em lote
		if(!is_array($id)){
			$url = $this->GetUrl().'/api/v3/payments/'.$id;
			return $this->DeleteAsaas($url); 
		}
		else{
			$boletos = array();
			foreach($id as $fr){
				$url = $this->GetUrl().'/api/v3/payments/'.$fr;
				$boletos[] = $this->DeleteAsaas($url); 
			}
			return $boletos;
		}
	}
	
	//______________________________________________

}

?>


