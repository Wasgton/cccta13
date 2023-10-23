create database cccat_13;
create table cccat_13.account(
     account_id CHAR(36) primary key,
     name varchar(255) not null,
     email varchar(255) not null,
     cpf varchar(11) not null,
     car_plate varchar(7) default null,
     is_passenger boolean not null default 1,
     is_driver boolean not null default 0,
     date timestamp not null,
     is_verified boolean not null default 0,
     verification_code CHAR(36) not null
);