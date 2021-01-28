# testeDeliver

Caminho dos arquivos que foram utilizados para o desenvolvimento do teste

api\source\index.php

api\source\src\deliver_teste\Controller\servicoTesteController.php

api\source\src\deliver_teste\Controller\deliverController.php

api\source\src\deliver_teste\Model\servicoTesteModel.php

api\source\src\deliver_teste\Model\deliverModel.php


Arquivos de configuração de ambiente

api\source\src\Core\Configure\Config.php


O arquivo readme_config.pdf mostra como configurar a API para receber as credenciais de acesso ao banco de dados e ao servidor local.

O arquivo readme_index.pdf mostra como executar os serviços da API via Postman para obter seus resultados.

Observações:
Acabei tendo alguns problemas ao utilizar o Docker pois estava desenvolvendo em ambiente windows, sendo assim optei por usar um servidor local (xampp) para o desenvolvimento do teste.
Os arquivos Dokerfile que estavam sendo utilizados para configuração do ambiente estão na raiz do projeto, porém como estava com dificuldades para a configuração do ambiente docker  acabei não utilizando-os.
Também na pasta raiz do projeto pode ser encontrado o arquivo (bd_deliver.sql) para criação do banco de dados.
O insert para a tabela oauth_client deve ser executado antes de executar qualquer outro serviço da API. O mesmo está no arquivo bd_deliver.sql
