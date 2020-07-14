create schema diagnostico collate utf8mb4_unicode_ci;
use diagnostico;
create table categories
(
    id int auto_increment
        primary key,
    name varchar(255) not null,
    description text null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null
);

create table indicator_group
(
    id int auto_increment
        primary key,
    categories_id int not null,
    name varchar(255) not null,
    description text null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null,
    constraint index_group_categories_id_fk
        foreign key (categories_id) references categories (id)
            on delete cascade
);

create table indicator
(
    id int auto_increment
        primary key,
    name varchar(255) null,
    indicator_group_id int not null,
    description text null,
    type varchar(10) null,
    updated timestamp null,
    created timestamp default CURRENT_TIMESTAMP not null,
    constraint indicator_indicator_group_id_fk
        foreign key (indicator_group_id) references indicator_group (id)
            on delete cascade
);

create index indicator_indicator_group_id_index
    on indicator (indicator_group_id);

create index index_group_categories_id_index
    on indicator_group (categories_id);

create table region
(
    id int auto_increment
        primary key,
    name varchar(255) not null,
    description text null,
    city tinyint(1) null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null
);

create table segmentation_group
(
    id int auto_increment
        primary key,
    name varchar(255) not null,
    description text null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null
);

create table indicator_segmentation_group
(
    id int auto_increment
        primary key,
    indicator_id int not null,
    segmentation_group_id int not null,
    constraint indicator_segmentation_group_indicator_id_fk
        foreign key (indicator_id) references indicator (id)
            on delete cascade,
    constraint indicator_segmentation_group_segmentation_group_id_fk
        foreign key (segmentation_group_id) references segmentation_group (id)
            on delete cascade
);

create index indicator_segmentation_group_indicator_id_index
    on indicator_segmentation_group (indicator_id);

create index indicator_segmentation_group_segmentation_group_id_index
    on indicator_segmentation_group (segmentation_group_id);

create table segmentation
(
    id int auto_increment
        primary key,
    segmentation_group_id int not null,
    name varchar(255) not null,
    description text null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null,
    constraint segmentation_segmentation_group_id_fk
        foreign key (segmentation_group_id) references segmentation_group (id)
            on delete cascade
);

create table indicator_value
(
    id int auto_increment
        primary key,
    indicator_id int not null,
    region_id int not null,
    segmentation_id int null,
    indicator_period varchar(40) not null,
    value text null,
    description text null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null,
    constraint IndicatorValue_indicator_id_fk
        foreign key (indicator_id) references indicator (id)
            on delete cascade,
    constraint IndicatorValue_region_id_fk
        foreign key (region_id) references region (id)
            on delete cascade,
    constraint IndicatorValue_segmentation_id_fk
        foreign key (segmentation_id) references segmentation (id)
            on delete cascade
);

create index IndicatorValue_indicator_id_index
    on indicator_value (indicator_id);

create index IndicatorValue_region_id_index
    on indicator_value (region_id);

create index IndicatorValue_segmentation_id_index
    on indicator_value (segmentation_id);

create index segmentation_segmentation_group_id_index
    on segmentation (segmentation_group_id);

create table user
(
    id int auto_increment
        primary key,
    name varchar(255) not null,
    role varchar(5) not null,
    password varchar(255) not null,
    created timestamp default CURRENT_TIMESTAMP not null,
    updated timestamp null,
    email varchar(255) not null,
    constraint user_email_uindex
        unique (email)
);

create table log
(
    id int auto_increment
        primary key,
    user_id int null,
    entity varchar(50) not null,
    entity_id int null,
    action varchar(255) not null,
    created timestamp default CURRENT_TIMESTAMP not null,
    constraint log_user_id_fk
        foreign key (user_id) references user (id)
            on delete set null
);

create index log_user_id_index
    on log (user_id);

insert into user (name, role, password, created, updated, email) values ('Uniplac', 'data', '$argon2id$v=19$m=1024,t=2,p=2$NFppdnl5WDl3QXlvZDhUZw$IDzTHEsLwJOSUxs0mWdcT8fjr9scpDoz026MORXlLM8', '2020-02-09 20:01:32', '2020-07-07 12:22:25', 'uniplac@uniplaclages.edu.br');
insert into user (name, role, password, created, updated, email) values ('Sabrina', 'admin', '$argon2id$v=19$m=1024,t=2,p=2$Vzc0LmlpUlQ2akxENWxQLw$gcmxkDfWk9lvlaAvdk0toGDPuxtYTyS0Kl2PxqK3XuQ', '2020-02-09 22:18:57', '2020-07-07 12:16:53', 'sabrina@uniplac.net');
insert into user (name, role, password, created, updated, email) values ('Bruno', 'mod', '$argon2id$v=19$m=1024,t=2,p=2$SU8vNS44eS96eTJMa1ZXWA$LKSwwfjGdwKhKlZjwSLnYRPrdn6rRMMzubrgpzJEuns', '2020-02-09 22:19:18', '2020-07-07 12:21:20', 'bruno@uniplaclages.edu.br');

insert into segmentation_group (id, name, description, created, updated) values (1, 'Sem segmentação', '', '2020-01-26 19:01:42', null);

