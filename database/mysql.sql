create table domain_events
(
    id char(36) not null primary key,
    aggregate_id varchar(191) not null,
    type varchar(191) not null,
    php_class varchar(191) not null,
    payload longtext not null,
    occurred_on datetime not null
);

create index domain_events_aggregate_id_index
    on domain_events (aggregate_id);

create index domain_events_type_occurred_on_index
    on domain_events (type, occurred_on);

create index domain_events_php_class_occurred_on_index
    on domain_events (php_class, occurred_on);

create index domain_events_occurred_on_index
    on domain_events (occurred_on);
