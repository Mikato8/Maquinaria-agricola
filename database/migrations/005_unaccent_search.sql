-- Búsqueda insensible a acentos: los vectores se generan con f_unaccent y las
-- consultas aplican la misma función al término buscado.

create or replace function f_unaccent(text) returns text
language sql immutable parallel safe strict as $$
    select public.unaccent('public.unaccent'::regdictionary, $1)
$$;

alter table machines drop column if exists search_vector;
alter table machines add column search_vector tsvector generated always as (
    to_tsvector('spanish', f_unaccent(
        coalesce(name, '') || ' ' || coalesce(model, '') || ' ' || coalesce(summary, '') || ' ' ||
        coalesce(description, '') || ' ' || coalesce(engine, '') || ' ' || coalesce(transmission, '')))
) stored;
create index if not exists machines_search_idx on machines using gin (search_vector);

alter table implements drop column if exists search_vector;
alter table implements add column search_vector tsvector generated always as (
    to_tsvector('spanish', f_unaccent(
        coalesce(name, '') || ' ' || coalesce(function, '') || ' ' || coalesce(summary, '') || ' ' ||
        coalesce(description, '') || ' ' || coalesce(operation, '')))
) stored;
create index if not exists implements_search_idx on implements using gin (search_vector);

alter table faults drop column if exists search_vector;
alter table faults add column search_vector tsvector generated always as (
    to_tsvector('spanish', f_unaccent(
        coalesce(title, '') || ' ' || coalesce(symptom, '') || ' ' ||
        coalesce(safety, '') || ' ' || coalesce(prevention, '')))
) stored;
create index if not exists faults_search_idx on faults using gin (search_vector);

alter table guides drop column if exists search_vector;
alter table guides add column search_vector tsvector generated always as (
    to_tsvector('spanish', f_unaccent(coalesce(title, '') || ' ' || coalesce(summary, '') || ' ' || coalesce(body, '')))
) stored;
create index if not exists guides_search_idx on guides using gin (search_vector);

alter table workshop_cases drop column if exists search_vector;
alter table workshop_cases add column search_vector tsvector generated always as (
    to_tsvector('spanish', f_unaccent(
        coalesce(title, '') || ' ' || coalesce(summary, '') || ' ' || coalesce(symptom, '') || ' ' ||
        coalesce(diagnosis, '') || ' ' || coalesce(repair, '')))
) stored;
create index if not exists workshop_search_idx on workshop_cases using gin (search_vector);
