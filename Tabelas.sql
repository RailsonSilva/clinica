CREATE TABLE agenda
(
   codigo int PRIMARY KEY,
   dataConsulta date,
   horario char (10),
   nome varchar (50),
   sexo char (10),
   email varchar(50),   
   codigoMedico int not null,
   FOREIGN KEY (codigoMedico) REFERENCES medico (codigo) ON DELETE CASCADE
);

CREATE TABLE medico
(
   codigo int PRIMARY KEY,
   especialidade varchar (50),
   crm char (15),
   FOREIGN KEY (codigo) REFERENCES funcionario (codigo) ON DELETE CASCADE
);

CREATE TABLE funcionario
(
   codigo int PRIMARY KEY,
   dataContrato date,
   salario char (10),
   senhaHash varchar (255),   
   FOREIGN KEY (codigo) REFERENCES pessoa (codigo) ON DELETE CASCADE
);

CREATE TABLE pessoa
(
   codigo int PRIMARY KEY,   
   nome varchar (100),
   sexo char (10),
   email varchar(100) UNIQUE,
   telefone char (20),
   cep char(20),
   logradouro varchar(100),
   cidade varchar(50),
   estado varchar(50)
);

CREATE TABLE paciente
(
   codigo int PRIMARY KEY,   
   peso float (10),
   altura int,
   tipoSanguineo char (5),
   FOREIGN KEY (codigo) REFERENCES pessoa (codigo) ON DELETE CASCADE
);

CREATE TABLE base_enderecos_ajax
(
   id int PRIMARY KEY,
   cep char(10) UNIQUE,
   logradouro varchar(100),
   cidade varchar(50),
   estado varchar(50)
);