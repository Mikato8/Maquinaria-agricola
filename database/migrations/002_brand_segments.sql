-- Amplía los segmentos de marca para cubrir forraje, ordeño y motocultores.

alter table brands drop constraint if exists brands_segment_check;
alter table brands add constraint brands_segment_check
    check (segment in ('tractor', 'cosechadora', 'pulverizadora', 'forrajera', 'motocultor', 'ordeno', 'implemento', 'mixto'));
