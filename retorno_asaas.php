//recebe o retorno através do webhook

$data = json_decode(file_get_contents("php://input"));
print_r($data);
$idasaas = $data->payment->id;
$installment = $data->payment->installment;
$valpago = $data->payment->value;
$vencbol = $data->payment->originalDueDate;
$confirmed = $data->event;
$forma_pagamento = $data->payment->billingType;



if($confirmed == "PAYMENT_RECEIVED" and $forma_pagamento =="BOLETO" and !empty($idasaas))
{
	m_php_baixa($idasaas, $forma_pagamento);	//cria métodos para dar baixa direto no sistema.
}
elseif($confirmed == "PAYMENT_CONFIRMED" and $forma_pagamento =="CREDIT_CARD" and !empty($idasaas))
{
	m_php_baixa($idasaas, $forma_pagamento); //cria métodos para dar baixa direto no sistema.
}
