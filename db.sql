create database cccat_13;
create table cccat_13.account(
     account_id binary(16) primary key,
     name varchar(255) not null,
     email varchar(255) not null,
     cpf varchar(11) not null,
     car_plate varchar(7) not null,
     is_passenger boolean not null,
     is_driver boolean not null,
     date timestamp not null,
     is_verified boolean not null,
     verification_code binary(16) not null
);