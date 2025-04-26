create table domain_events
(
    id char(36) not null primary key,
    aggregate_id varchar(191) not null,
    published boolean not null default false,
    type varchar(191) not null,
    php_class varchar(191) not null,
    payload longtext not null,
    occurred_on datetime not null
);

create index domain_events_published_index
    on domain_events (published, occurred_on);

create index domain_events_aggregate_id_index
    on domain_events (aggregate_id);

create index domain_events_type_occurred_on_index
    on domain_events (type, occurred_on);

create index domain_events_php_class_occurred_on_index
    on domain_events (php_class, occurred_on);

create index domain_events_occurred_on_index
    on domain_events (occurred_on);

create table domain_events_handled
(
    event_id char(36) not null,
    handler_class varchar(191) not null,
    handled_at timestamp default CURRENT_TIMESTAMP not null,
    constraint domain_events_handled_event_id_handler_class_unique
        unique (event_id, handler_class)
);
