create table users
(
  id bigserial not null primary key,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  first_name text not null,
  last_name text not null,
  email text not null unique,
  password character varying(60) not null,
  password_token text,
  image_url text
) without oids;

create table types
(
  id bigserial not null primary key,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  name text not null unique,
  icon text not null unique,
  ord smallint not null default 0
) without oids;

insert into types values (default, default, 1, default, 1, 'Necesary', 'content_paste', 1);
insert into types values (default, default, 1, default, 1, 'Unnecesary', 'add_to_queue', 2);
insert into types values (default, default, 1, default, 1, 'Obligatory', 'group', 3);

create table categories
(
  id bigserial not null primary key,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  name text not null unique,
  icon text not null unique,
  ord smallint not null default 0
) without oids;

create table payment_methods
(
  id bigserial not null,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  name text not null,
  icon text not null,
  ord smallint not null default 0,
  constraint payment_methods_pkey primary key (id),
  constraint payment_methods_uk1 UNIQUE (name),
  constraint payment_methods_uk2 UNIQUE (icon)
) without oids;

create table spends
(
  id bigserial not null primary key,
  created_at timestamp without time zone not null default now(),
  created_by bigint not null,
  modified_at timestamp without time zone default now(),
  modified_by bigint,
  amount numeric(7, 2) not null,
  date timestamp without time zone not null default now(),
  concept text not null,
  description text,
  type_id bigint not null references types (id),
  category_id bigint not null references categories (id),
  payment_method_id bigint not null references payment_methods (id)
) without oids;
