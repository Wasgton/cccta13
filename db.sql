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

create table cccat_13.ride
(
    ride_id      char(36)                            not null
        primary key,
    passenger_id char(36)                            not null,
    driver_id    char(36)                            null,
    status       text                                null,
    fare         decimal                             null,
    distance     decimal                             null,
    from_lat     decimal(18, 14)                     null,
    from_long    decimal(18, 14)                     null,
    to_lat       decimal(18, 14)                     null,
    to_long      decimal(18, 14)                     null,
    date         timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint ride_account_account_id_fk
        foreign key (passenger_id) references cccat_13.account (account_id)
);

