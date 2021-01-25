<?php
namespace API\deliver_teste\Controller;
use Silex\Application;
use API\Core\Auth\AuthServerController;
use API\Core\Configure\Config;


class deliverController
{

	protected function validarRequisicao($app,$id='',$origem,$scope=null)
	{	
		// Verifica se é desenvolvedor para não ficar solicitando acesso ao token toda hora
		if(!Config::isDeveloper()){
			$auth = new AuthServerController();
			$tokenRequest = $auth->validateUserRequest($scope);
			if($tokenRequest->getStatusCode() != 200){
				$tokenRequest->send();
				exit ;
			}
			$token  = $auth->getAccessTokenData();
			switch($origem){
				case 'aluno':
				if(!empty($token['user_id']) && $token['user_id'] != $id ){
					$app->json(array('codigo'=>403,'mensagem'=>utf8_encode('O token não pertence a este Registro')),403)->send();
					exit;
				}
				break;
				case 'professor':
				if(!empty($token['client_id']) && $token['user_id'] != $id){
					echo $app->json(array('codigo'=>403,'mensagem'=>utf8_encode('O token não pertence a esta pessoa')),403);
					exit;
				}
				break;
			}
		}
		return true;
	}
}
