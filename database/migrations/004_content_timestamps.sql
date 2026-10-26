-- Marcas de tiempo para sitemap y actualizaciones editoriales.

alter table tech_resources add column if not exists updated_at timestamptz not null default now();
alter table providers add column if not exists updated_at timestamptz not null default now();
