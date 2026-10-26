-- Permite la categoría de electrónica y diagnóstico en recursos tecnológicos.

alter table tech_resources drop constraint if exists tech_resources_category_check;
alter table tech_resources add constraint tech_resources_category_check
    check (category in ('guiado', 'telemetria', 'gestion', 'isobus', 'vision', 'nlp', 'teledeteccion',
                        'datos', 'robotica', 'riego', 'taller', 'electronica'));
