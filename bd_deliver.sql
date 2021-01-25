CREATE TABLE OAUTH_CLIENTS (
 client_id  VARCHAR(80) NOT NULL,
 client_secret  VARCHAR(80) NOT NULL,
 client_name varchar(100) not null,
 client_image blob,
 redirect_uri VARCHAR(2000),
 grant_types  VARCHAR(80),
 scope VARCHAR(4000),
 user_id  VARCHAR(80),
 PRIMARY KEY (client_id)
 );

 CREATE TABLE  OAUTH_ACCESS_TOKENS  (
 access_token  VARCHAR(40)  NOT NULL,
 client_id VARCHAR(80)  NOT NULL,
 user_id   VARCHAR(80),
 expires   TIMESTAMP  NOT NULL,
 scope   VARCHAR(4000),
 PRIMARY KEY (access_token)
 );

 CREATE TABLE  OAUTH_AUTHORIZATION_CODES  (
 authorization_code  VARCHAR(40)  NOT NULL,
 client_id  VARCHAR(80)  NOT NULL,
 user_id  VARCHAR(80),
 redirect_uri VARCHAR(2000),
 expires  TIMESTAMP  NOT NULL,
 scope  VARCHAR(4000),
 id_token VARCHAR(1000),
 PRIMARY KEY (authorization_code)
 );

 CREATE TABLE  OAUTH_REFRESH_TOKENS  (
 refresh_token   VARCHAR(40)  NOT NULL,
 client_id  VARCHAR(80)  NOT NULL,
 user_id  VARCHAR(80),
 expires  TIMESTAMP  NOT NULL,
 scope  VARCHAR(4000),
 PRIMARY KEY (refresh_token)
 );

 CREATE TABLE  OAUTH_USERS  (
 username VARCHAR(80),
 password VARCHAR(80),
 first_name VARCHAR(80),
 last_name  VARCHAR(80),
 email  VARCHAR(80),
 email_verified  BOOLEAN,
 scope  VARCHAR(4000)
 );

 CREATE TABLE  OAUTH_SCOPES  (
 scope  VARCHAR(80)  NOT NULL,
 is_default BOOLEAN,
 PRIMARY KEY (scope)
 );

 CREATE TABLE  OAUTH_JWT  (
 client_id  VARCHAR(80) NOT NULL,
 subject  VARCHAR(80),
 public_key VARCHAR(2000) NOT NULL
 );

 CREATE TABLE  jti_table  (
 issuer   VARCHAR(80) NOT NULL,
 subject  VARCHAR(80),
 audiance VARCHAR(80),
 expires  TIMESTAMP   NOT NULL,
 jti VARCHAR(2000) NOT NULL
 );

 CREATE TABLE  public_key_table  (
 client_id VARCHAR(80),
 public_key  VARCHAR(2000),
 private_key VARCHAR(2000),
 encryption_algorithm VARCHAR(100) DEFAULT 'RS256'
 );


insert into oauth_clients (client_id,client_secret,client_name,client_image,redirect_uri,grant_types,scope,user_id) values('201604191641app.deliver.com.br','QXTVPOW1WCRDPEBZ620W','deliver','','http://callbackurl.com','client_credentials','','');

 
  CREATE TABLE pessoa(
 id_pessoa int unsigned auto_increment not null,
 nome varchar(255),
 cpf  varchar(14),
 data_nascimento date,
 data_criacao datetime,
 data_alteracao datetime,
 primary key (id_pessoa)
 );
 
 
 
 insert into pessoa (nome,cpf,data_nascimento,data_criacao,data_alteracao) values ('Barry Allen','652.365.987-99','1994-08-03',sysdate(),sysdate());
 
 select * from pessoa;
 
  CREATE TABLE corredor(
 id_corredor int unsigned auto_increment not null,
 id_pessoa int unsigned not null,
 data_criacao datetime,
 data_alteracao datetime,
 primary key (id_corredor),
 foreign key (id_pessoa) references pessoa(id_pessoa)
 );
 
 insert into corredor (id_pessoa,data_criacao,data_alteracao) values(2,sysdate(),sysdate());
 
 select * from corredor;
 
 CREATE TABLE prova(
 id_prova int unsigned auto_increment not null,
 tipo_prova varchar(10),
 data_prova datetime,
 data_criacao datetime,
 data_alteracao datetime,
 primary key (id_prova)
 );
 
 insert into prova (tipo_prova,data_prova,data_criacao,data_alteracao) values('5 km',sysdate(),sysdate(),sysdate());
 
  select * from prova;
  
   CREATE TABLE prova_corredor(
id_prova_corredor int unsigned auto_increment not null,
id_prova int unsigned not null,
id_corredor int unsigned not null,
data_criacao datetime,
data_alteracao datetime,
primary key (id_prova_corredor),
foreign key (id_prova) references prova(id_prova),
foreign key (id_corredor) references corredor(id_corredor)
 );
 
  insert into prova_corredor (id_prova,id_corredor,data_criacao,data_alteracao) values(1,1,sysdate(),sysdate());
 
  select * from prova;
  
CREATE TABLE resultados(
id_resultados int unsigned auto_increment not null,
id_prova_corredor int unsigned not null,
hora_inicio_prova time,
hora_conclusao_prova time,
data_criacao datetime,
data_alteracao datetime,
primary key (id_resultados),
foreign key (id_prova_corredor) references prova_corredor(id_prova_corredor)
 );
 
insert into resultados (id_prova_corredor,hora_inicio_prova,hora_conclusao_prova,data_criacao,data_alteracao) values(1,'2021-01-24 09:00:00','2021-01-24 12:00:00',sysdate(),sysdate());
 
select * from resultados;